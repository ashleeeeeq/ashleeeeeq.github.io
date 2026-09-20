<?php

namespace App\Jobs;

use App\Contracts\LlmClientInterface;
use App\Models\Report;
use App\Services\Reports\ReportContextBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;

    public function __construct(
        public int $reportId,
    ) {}

    public function failed(\Throwable $e): void
    {
        Log::error('report.job.crashed', [
            'report_id' => $this->reportId,
            'error' => $e->getMessage(),
        ]);

        Report::whereKey($this->reportId)->update([
            'status' => 'failed',
            'error_message' => $e->getMessage(),
        ]);
    }

    public function handle(
        LlmClientInterface $llm,
        ReportContextBuilder $contextBuilder,
    ): void {
        $report = Report::with('user')->find($this->reportId);

        if (!$report || !$report->isGenerating()) {
            return;
        }

        try {
            $report->update(['status' => 'generating', 'generation_phase' => 'generating_narratives']);

            $previousNarratives = is_array($report->narrative_cache) ? $report->narrative_cache : null;

            $t0 = microtime(true);
            $narratives = $this->generateNarratives($report, $llm, $contextBuilder);
            Log::info('report.generation.narratives', [
                'report_id' => $report->id,
                'duration_ms' => (int) ((microtime(true) - $t0) * 1000),
            ]);

            if ($previousNarratives !== null && $previousNarratives == $narratives && $report->pdf_path !== null) {
                Log::info('report.generation.pdf_skipped_unchanged', [
                    'report_id' => $report->id,
                ]);
            } else {
                $t1 = microtime(true);
                $this->generatePdf($report, $narratives);
                Log::info('report.generation.pdf', [
                    'report_id' => $report->id,
                    'duration_ms' => (int) ((microtime(true) - $t1) * 1000),
                ]);
            }

            $report->update(['status' => 'completed', 'generation_phase' => null]);
        } catch (\Throwable $e) {
            Log::error('Report generation failed', [
                'report_id' => $report->id,
                'type' => $report->type,
                'period' => $report->periodLabel(),
                'generation_phase' => $report->generation_phase,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $report->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    private function generateNarratives(Report $report, LlmClientInterface $llm, ReportContextBuilder $contextBuilder): array
    {
        $prompts = $contextBuilder->buildPrompts($report);

        $batchPrompts = [];
        foreach ($prompts as $key => $prompt) {
            $batchPrompts[$key] = [
                'system'  => $prompt['system'],
                'user'    => $prompt['user'],
                'context' => ['report_id' => $report->id, 'section' => $key],
            ];
        }

        $report->update(['generation_phase' => 'generating_narratives']);

        $narratives = $llm->generateBatch($batchPrompts);

        $report->update(['narrative_cache' => $narratives]);

        return $narratives;
    }

    private function generatePdf(Report $report, array $narratives): void
    {
        $report->update(['generation_phase' => 'rendering_pdf']);

        $html = view('reports.pdf', [
            'report' => $report,
            'narratives' => $narratives,
            'payload' => $report->boot_payload ?? [],
        ])->render();

        $manifestPath = public_path('build/manifest.json');
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            $entry = $manifest['resources/css/app.css'] ?? [];
            $cssFiles = $entry['css'] ?? [];
            if (empty($cssFiles) && isset($entry['file'])) {
                $cssFiles = [$entry['file']];
            }
            $cssParts = [];
            foreach ($cssFiles as $f) {
                $cssPath = public_path('build/' . $f);
                if (file_exists($cssPath)) {
                    $cssParts[] = file_get_contents($cssPath);
                }
            }
            if ($cssParts) {
                $html = str_replace('</head>', '<style>' . implode("\n", $cssParts) . '</style></head>', $html);
            }
        }

        $http = Http::timeout(config('services.gotenberg.timeout', 60));

        $username = config('services.gotenberg.username');
        $password = config('services.gotenberg.password');
        if ($username && $password) {
            $http = $http->withBasicAuth($username, $password);
        }

        $http = $http->attach('files', $html, 'index.html');

        $chartJsPath = base_path('node_modules/chart.js/dist/chart.umd.js');
        if (file_exists($chartJsPath)) {
            $http = $http->attach('files', file_get_contents($chartJsPath), 'chart.umd.js');
        }

        $this->warmUpGotenberg();

        $response = $http->post(rtrim(config('services.gotenberg.url', 'http://localhost:3000'), '/') . '/forms/chromium/convert/html', [
            'skipNetworkIdleEvent' => 'true',
            'waitForExpression' => 'window.status === "ready"',
            'printBackground' => 'true',
            'preferCssPageSize' => 'true',
            'marginTop' => '0',
            'marginBottom' => '0',
            'marginRight' => '0',
            'marginLeft' => '0',
        ]);

        if ($response->failed()) {
            Log::error('report.pdf.gotenberg_failed', [
                'report_id' => $report->id,
                'status' => $response->status(),
                'body' => Str::limit($response->body(), 300),
            ]);

            throw new \RuntimeException('Gotenberg PDF conversion failed: ' . $response->body());
        }

        $path = 'reports/pdf/report-' . $report->id . '-' . Str::random(8) . '.pdf';

        Storage::disk('s3')->put($path, $response->body(), 'private');

        $report->update(['pdf_path' => $path]);
    }

    private function warmUpGotenberg(): void
    {
        if (Cache::has('gotenberg.warmed')) {
            return;
        }

        try {
            Http::timeout(5)
                ->withBasicAuth(
                    config('services.gotenberg.username'),
                    config('services.gotenberg.password'),
                )
                ->get(rtrim(config('services.gotenberg.url', 'http://localhost:3000'), '/') . '/health');
        } catch (\Throwable) {
            // Cold start initiated — timeout expected if service was asleep
        }

        Cache::put('gotenberg.warmed', true, now()->addMinutes(5));
    }
}
