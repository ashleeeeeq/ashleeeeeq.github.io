<?php

use App\Services\Llm\GroqClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

uses(Tests\TestCase::class);

beforeEach(function () {
    Cache::flush();

    config([
        'services.groq.api_key' => 'test-key',
        'services.groq.model' => 'primary-model,fallback-model',
        'services.groq.timeout' => 5,
        'services.groq.retry_attempts' => 2,
        'services.groq.batch_retry_attempts' => 1,
        'services.groq.retry_delay_ms' => 1,
        'services.groq.pool_chunk' => 5,
        'services.groq.fallback_cooldown_ms' => 0,
        'services.groq.cache_ttl' => 60,
    ]);
});

function batchPrompts(): array
{
    return [
        'section_a' => ['system' => 'sys a', 'user' => 'user a', 'context' => ['report_id' => 42, 'section' => 'section_a']],
        'section_b' => ['system' => 'sys b', 'user' => 'user b', 'context' => ['report_id' => 42, 'section' => 'section_b']],
    ];
}

it('falls back to the next model immediately when the primary model fails', function () {
    Http::fake(function ($request) {
        if ($request['model'] === 'primary-model') {
            return Http::response(['error' => ['message' => 'overloaded']], 500);
        }

        return Http::response([
            'choices' => [['message' => ['content' => 'Recovered narrative.']]],
        ], 200);
    });

    Log::shouldReceive('info')->once()->withArgs(function (string $event, array $ctx) {
        return $event === 'report.llm.batch_complete'
            && $ctx['report_id'] === 42
            && $ctx['total'] === 2
            && $ctx['cached'] === 0
            && $ctx['generated'] === 2
            && $ctx['failed'] === 0;
    });
    Log::shouldReceive('warning')->never();

    $result = (new GroqClient())->generateBatch(batchPrompts());

    expect($result['section_a'])->toBe('Recovered narrative.');
    expect($result['section_b'])->toBe('Recovered narrative.');
});

it('makes one attempt per model in batch mode', function () {
    Http::fake(['*' => Http::response(['error' => ['message' => 'down']], 500)]);

    $result = (new GroqClient())->generateBatch(batchPrompts());

    expect($result['section_a'])->toBe(GroqClient::UNAVAILABLE_PLACEHOLDER);
    expect($result['section_b'])->toBe(GroqClient::UNAVAILABLE_PLACEHOLDER);

    expect(count(Http::recorded()))->toBe(4);
});

it('logs per-section attempt history when all models fail', function () {
    Http::fake(['*' => Http::response(['error' => ['message' => 'quota exhausted']], 429)]);

    Log::shouldReceive('warning')->twice()->withArgs(function (string $event, array $ctx) {
        if ($event !== 'report.llm.section_failed' || $ctx['report_id'] !== 42) {
            return false;
        }

        $attempts = $ctx['attempts'];

        return count($attempts) === 2
            && $attempts[0]['model'] === 'primary-model'
            && $attempts[1]['model'] === 'fallback-model'
            && $attempts[0]['status'] === 429
            && $attempts[0]['reason'] === 'http_429'
            && str_contains($attempts[0]['body'], 'quota exhausted');
    });

    Log::shouldReceive('info')->once()->withArgs(function (string $event, array $ctx) {
        return $event === 'report.llm.batch_complete' && $ctx['failed'] === 2;
    });

    $result = (new GroqClient())->generateBatch(batchPrompts());

    expect($result['section_a'])->toBe(GroqClient::UNAVAILABLE_PLACEHOLDER);
    expect($result['section_b'])->toBe(GroqClient::UNAVAILABLE_PLACEHOLDER);
});
