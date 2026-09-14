<?php

namespace App\Console\Commands;

use App\Models\Program;
use App\Services\DashboardMetricService;
use App\Services\StaffDashboardService;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('dashboard:refresh-metrics')]
#[Description('Pre-compute dashboard metrics for the summary table')]
class RefreshDashboardMetrics extends Command
{
    public function handle(
        StaffDashboardService $dashboardService,
        DashboardMetricService $metricService,
    ): int {
        $currentYear = (int) now()->year;
        $years = range($currentYear - 5, $currentYear + 1);
        $programs = Program::all();

        if ($programs->isEmpty()) {
            $this->warn('No programs found.');

            return Command::FAILURE;
        }

        foreach ($programs as $program) {
            $this->info("Processing program: {$program->program_name}");

            foreach ($years as $year) {
                $months = $year === $currentYear ? range(1, 12) : [null];

                foreach ($months as $month) {
                    $metrics = $dashboardService->collectMetricsForSummary(
                        $program->id,
                        $year,
                        $month,
                    );

                    if ($metrics !== []) {
                        $metricService->bulkUpsert($metrics);
                        $label = $month ? "{$year}-{$month}" : (string) $year;
                        $this->line("  Cached {$label}: " . count($metrics) . ' metrics');
                    }
                }
            }
        }

        $this->info('Dashboard metrics refreshed successfully.');

        return Command::SUCCESS;
    }
}
