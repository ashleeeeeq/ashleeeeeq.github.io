<?php

namespace App\Services\Llm;

use App\Contracts\LlmClientInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GroqClient implements LlmClientInterface
{
    public const UNAVAILABLE_PLACEHOLDER = 'Narrative generation unavailable for this section.';

    private const MAX_RETRY_DELAY_MS = 15000;

    private string $apiKey;
    private string $baseUrl;
    private array $models;
    private string $primaryModel;
    private int $timeout;
    private float $temperature;
    private int $maxTokens;
    private float $frequencyPenalty;
    private float $presencePenalty;
    private float $topP;
    private int $retryAttempts;
    private int $batchRetryAttempts;
    private int $retryDelayMs;
    private int $poolChunkSize;
    private int $fallbackCooldownMs;

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key');
        $this->baseUrl = rtrim(config('services.groq.base_url', 'https://api.groq.com/openai/v1'), '/');
        $this->models = array_map('trim', explode(',', config('services.groq.model')));
        $this->primaryModel = $this->models[0];
        $this->timeout = (int) config('services.groq.timeout', 120);
        $this->temperature = (float) config('services.groq.temperature', 0.7);
        $this->maxTokens = (int) config('services.groq.max_tokens', 1024);
        $this->frequencyPenalty = (float) config('services.groq.frequency_penalty', 0.5);
        $this->presencePenalty = (float) config('services.groq.presence_penalty', 0.3);
        $this->topP = (float) config('services.groq.top_p', 0.9);
        $this->retryAttempts = max(1, (int) config('services.groq.retry_attempts', 2));
        $this->batchRetryAttempts = max(1, (int) config('services.groq.batch_retry_attempts', 1));
        $this->retryDelayMs = (int) config('services.groq.retry_delay_ms', 1500);
        $this->poolChunkSize = max(1, (int) config('services.groq.pool_chunk', 5));
        $this->fallbackCooldownMs = (int) config('services.groq.fallback_cooldown_ms', 3000);
    }

    public function generate(string $systemPrompt, string $userMessage, array $context = []): string
    {
        $cacheKey = $this->cacheKey($systemPrompt, $userMessage, $context);
        $ttl = (int) config('services.groq.cache_ttl', 86400);

        return Cache::remember($cacheKey, now()->addSeconds($ttl), function () use ($systemPrompt, $userMessage) {
            return $this->callGroq($systemPrompt, $userMessage);
        });
    }

    private function callGroq(string $systemPrompt, string $userMessage): string
    {
        $lastException = null;

        foreach ($this->models as $model) {
            try {
                return $this->callGroqWithModel($model, $systemPrompt, $userMessage);
            } catch (RequestException $e) {
                Log::warning('Groq model failed (transport)', [
                    'model' => $model,
                    'status' => $e->response?->status(),
                    'message' => $e->getMessage(),
                ]);
                $lastException = $e;
            } catch (\Throwable $e) {
                Log::warning('Groq model failed', [
                    'model' => $model,
                    'message' => $e->getMessage(),
                ]);
                $lastException = $e;
            }
        }

        throw new \RuntimeException('All Groq models failed: ' . ($lastException?->getMessage() ?? 'unknown error'));
    }

    private function callGroqWithModel(string $model, string $systemPrompt, string $userMessage): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout($this->timeout)
            ->retry(
                $this->retryAttempts,
                fn (int $attempt, ?Throwable $e) => $this->retryDelayMs($attempt, $e),
                when: fn (Throwable $e) => $this->isTransientFailure($e),
                throw: false,
            )
            ->post($this->baseUrl . '/chat/completions', $this->buildPayload($model, $systemPrompt, $userMessage));

        if ($response->failed()) {
            throw new \RuntimeException('Groq API request failed: ' . $response->body());
        }

        $data = $response->json();

        $content = $this->sanitizeContent($data['choices'][0]['message']['content'] ?? '');
        if (empty($content)) {
            throw new \RuntimeException('Groq API returned empty content for model: ' . $model);
        }

        return $content;
    }

    private function buildPayload(string $model, string $systemPrompt, string $userMessage): array
    {
        $payload = [
            'model'             => $model,
            'messages'          => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage],
            ],
            'temperature'       => $this->temperature,
            'max_tokens'        => $this->maxTokens,
            'frequency_penalty' => $this->frequencyPenalty,
            'presence_penalty'  => $this->presencePenalty,
            'top_p'             => $this->topP,
        ];

        if (str_contains($model, 'gpt-oss')) {
            $includeReasoning = config('services.groq.include_reasoning');
            if ($includeReasoning !== null) {
                $payload['include_reasoning'] = filter_var($includeReasoning, FILTER_VALIDATE_BOOLEAN);
            }
            if ($effort = config('services.groq.reasoning_effort')) {
                $payload['reasoning_effort'] = $effort;
            }
        } elseif ($format = config('services.groq.reasoning_format')) {
            $payload['reasoning_format'] = $format;
        }

        return $payload;
    }

    private function sanitizeContent(string $content): string
    {
        return trim(preg_replace('/<think>.*?<\/think>/s', '', $content) ?? '');
    }

    public function generateBatch(array $prompts): array
    {
        $results = [];
        $uncached = [];
        $cacheTtl = (int) config('services.groq.cache_ttl', 86400);

        foreach ($prompts as $key => $prompt) {
            $cacheKey = $this->cacheKey(
                $prompt['system'] ?? '',
                $prompt['user'] ?? '',
                $prompt['context'] ?? []
            );
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                $results[$key] = $cached;
            } else {
                $uncached[$key] = [
                    'cache_key' => $cacheKey,
                    'system'    => $prompt['system'] ?? '',
                    'user'      => $prompt['user'] ?? '',
                    'context'   => $prompt['context'] ?? [],
                ];
            }
        }

        $failedKeys = !empty($uncached)
            ? $this->generateBatchViaPool($uncached, $results, $cacheTtl)
            : [];

        $cachedCount = count($prompts) - count($uncached);

        Log::info('report.llm.batch_complete', [
            'report_id' => $this->reportIdFromItems($prompts),
            'total' => count($prompts),
            'cached' => $cachedCount,
            'generated' => count($prompts) - $cachedCount - count($failedKeys),
            'failed' => count($failedKeys),
        ]);

        $ordered = [];
        foreach ($prompts as $key => $prompt) {
            $ordered[$key] = $results[$key] ?? self::UNAVAILABLE_PLACEHOLDER;
        }

        return $ordered;
    }

    private function generateBatchViaPool(array $uncached, array &$results, int $cacheTtl): array
    {
        $reportId = $this->reportIdFromItems($uncached);
        $history = [];

        $round = $this->poolRound($uncached, $this->primaryModel, $results, $cacheTtl);
        foreach ($round['errors'] as $key => $detail) {
            $history[$key][] = $detail;
        }
        $failures = $round['failed'];

        $fallbacks = array_slice($this->models, 1);
        foreach ($fallbacks as $model) {
            if (empty($failures)) {
                break;
            }

            if ($round['rate_limited'] && $this->fallbackCooldownMs > 0) {
                usleep($this->fallbackCooldownMs * 1000);
            }

            $round = $this->poolRound($failures, $model, $results, $cacheTtl);

            foreach ($round['errors'] as $key => $detail) {
                $history[$key][] = $detail;
            }
            $failures = $round['failed'];
        }

        foreach ($failures as $key => $item) {
            Log::warning('report.llm.section_failed', [
                'report_id' => $reportId,
                'section' => $key,
                'attempts' => $history[$key] ?? [],
            ]);

            $results[$key] = self::UNAVAILABLE_PLACEHOLDER;
        }

        return array_keys($failures);
    }

    private function reportIdFromItems(array $items): ?int
    {
        foreach ($items as $item) {
            $reportId = $item['context']['report_id'] ?? null;

            if ($reportId !== null) {
                return (int) $reportId;
            }
        }

        return null;
    }

    private function poolRound(array $items, string $model, array &$results, int $cacheTtl): array
    {
        $failed = [];
        $errors = [];
        $rateLimited = false;

        foreach (array_chunk($items, $this->poolChunkSize, preserve_keys: true) as $chunk) {
            $chunkResult = $this->dispatchPoolChunk($chunk, $model, $results, $cacheTtl);

            $failed += $chunkResult['failed'];
            $errors += $chunkResult['errors'];
            $rateLimited = $rateLimited || $chunkResult['rate_limited'];
        }

        return ['failed' => $failed, 'rate_limited' => $rateLimited, 'errors' => $errors];
    }

    private function dispatchPoolChunk(array $items, string $model, array &$results, int $cacheTtl): array
    {
        $responses = Http::pool(function (Pool $pool) use ($items, $model) {
            foreach ($items as $key => $item) {
                $pool->as($key)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type'  => 'application/json',
                    ])
                    ->timeout($this->timeout)
                    ->retry(
                        $this->batchRetryAttempts,
                        fn (int $attempt, ?Throwable $e) => $this->retryDelayMs($attempt, $e),
                        when: fn (Throwable $e) => $this->isTransientFailure($e),
                        throw: false,
                    )
                    ->post($this->baseUrl . '/chat/completions', $this->buildPayload($model, $item['system'], $item['user']));
            }
        });

        $failed = [];
        $errors = [];
        $rateLimited = false;
        foreach ($items as $key => $item) {
            $response = $responses[$key] ?? null;
            if ($response instanceof Response && $response->ok()) {
                $data = $response->json();
                $content = $this->sanitizeContent($data['choices'][0]['message']['content'] ?? '');
                if (!empty($content)) {
                    Cache::put($item['cache_key'], $content, now()->addSeconds($cacheTtl));
                    $results[$key] = $content;
                    continue;
                }

                $errors[$key] = ['model' => $model, 'status' => 200, 'reason' => 'empty_content', 'body' => null];
                $failed[$key] = $item;
                continue;
            }

            if ($response instanceof Response) {
                $status = $response->status();
                $errors[$key] = [
                    'model' => $model,
                    'status' => $status,
                    'reason' => 'http_' . $status,
                    'body' => $this->truncateBody($response->body()),
                ];

                if ($status === 429) {
                    $rateLimited = true;
                }
            } elseif ($response instanceof Throwable) {
                $errors[$key] = [
                    'model' => $model,
                    'status' => null,
                    'reason' => 'connection_error',
                    'body' => $this->truncateBody($response->getMessage()),
                ];
            } else {
                $errors[$key] = ['model' => $model, 'status' => null, 'reason' => 'no_response', 'body' => null];
            }

            $failed[$key] = $item;
        }

        return ['failed' => $failed, 'rate_limited' => $rateLimited, 'errors' => $errors];
    }

    private function truncateBody(?string $body): ?string
    {
        if ($body === null || $body === '') {
            return null;
        }

        return strlen($body) > 300 ? substr($body, 0, 300) . '…' : $body;
    }

    private function isTransientFailure(Throwable $e): bool
    {
        if ($e instanceof ConnectionException) {
            return true;
        }

        if ($e instanceof RequestException) {
            $status = $e->response?->status();

            return $status === null || $status >= 500 || $status === 429;
        }

        return false;
    }

    private function retryDelayMs(int $attempt, ?Throwable $e): int
    {
        if ($e instanceof RequestException && $e->response?->status() === 429) {
            $retryAfter = $e->response->header('Retry-After');
            $seconds = is_array($retryAfter) ? ($retryAfter[0] ?? null) : $retryAfter;

            if (is_numeric($seconds)) {
                return min((int) $seconds * 1000, self::MAX_RETRY_DELAY_MS);
            }
        }

        return min($this->retryDelayMs * $attempt, self::MAX_RETRY_DELAY_MS);
    }

    private function cacheKey(string $systemPrompt, string $userMessage, array $context): string
    {
        $hash = md5($systemPrompt . '||' . $userMessage . '||' . md5(serialize($context)));

        return 'groq.response.' . $hash;
    }
}
