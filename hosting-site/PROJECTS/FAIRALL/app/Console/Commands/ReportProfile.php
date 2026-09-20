<?php

namespace App\Console\Commands;

use App\Enums\ReportPeriod;
use App\Enums\ReportType;
use App\Models\Program;
use App\Models\User;
use App\Services\Reports\ReportGenerationService;
use App\Services\StaffDashboardService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportProfile extends Command
{
    protected $signature = 'report:profile
        {--type=education : Report type (education, sports, funding, multi_section)}
        {--year= : Year for the report}
        {--quarter= : Quarter (1-4) for quarterly report}
        {--from= : Start year for multi-year}
        {--to= : End year for multi-year}
        {--program= : Filter by program name}
        {--no-cache : Bypass Laravel cache (always run fresh queries)}';

    protected $description = 'Profile report generation timing and database queries';

    private array $queryLog = [];

    public function handle(
        ReportGenerationService $generationService,
        StaffDashboardService $dashboardService,
    ): int {
        $type = $this->resolveType();
        $periodType = $this->resolvePeriodType();
        $periodConfig = $this->buildPeriodConfig($periodType);
        $dates = $this->resolveDates($periodConfig);
        $program = $this->resolveProgram($type);

        $user = User::where('email', 'admin@example.com')->first();
        if (!$user) {
            $this->error('No admin user found. Run db:seed first.');
            return Command::FAILURE;
        }

        $this->line('');
        $this->info('═══════════════════════════════════════════════');
        $this->info(' Configuration');
        $this->line('  Report type:    ' . $type->value);
        $this->line('  Period type:    ' . $periodType->value);
        $this->line('  Date range:     ' . $dates['start']->toDateString() . ' → ' . $dates['end']->toDateString());
        $this->line('  Program:        ' . ($program?->program_name ?? 'All'));
        $this->info('═══════════════════════════════════════════════');

        $this->profileBoot($generationService, $type, $periodConfig, $user);

        if ($this->option('no-cache')) {
            $cacheKey = 'report.boot.' . implode('.', [
                $type->value,
                $dates['start']->toDateString(),
                $dates['end']->toDateString(),
                $program?->id ?? 'all',
            ]);
            Cache::forget($cacheKey);

            $this->profileFreshPayload($dashboardService, $type, $dates, $program);
        }

        if ($program) {
            $this->profileMetricsByProgram($dashboardService, $program);
        } else {
            foreach (Program::all() as $prog) {
                $this->profileMetricsByProgram($dashboardService, $prog);
            }
        }

        $this->newLine();
        $this->info('Profiling complete.');
        return Command::SUCCESS;
    }

    private function profileBoot(
        ReportGenerationService $generationService,
        ReportType $type,
        array $periodConfig,
        User $user,
    ): void {
        $this->newLine();
        $this->info('── 1. Boot Phase (with Cache::remember) ──────');

        $this->captureQueries();
        $start = hrtime(true);
        $payload = $generationService->boot($type, $periodConfig, $user);
        $elapsedMs = (int) ((hrtime(true) - $start) / 1_000_000);
        $this->releaseQueries();

        $this->line('  Wall time:      ' . $elapsedMs . ' ms');

        if ($payload) {
            $sections = [];
            foreach (['education', 'sports', 'funding', 'educationActivity', 'sportsActivity'] as $key) {
                if (isset($payload[$key])) {
                    $sections[] = $key;
                }
            }
            $this->line('  Sections:       ' . implode(', ', $sections));
        }

        $this->printQuerySummary();
    }

    private function profileFreshPayload(
        StaffDashboardService $dashboardService,
        ReportType $type,
        array $dates,
        ?Program $program,
    ): void {
        $this->newLine();
        $this->info('── 2. Fresh Payload (bypassing Cache) ─────────');

        $this->captureQueries();
        $start = hrtime(true);
        $payload = $dashboardService->buildReportPayload($type, $dates['start'], $dates['end'], $program);
        $elapsedMs = (int) ((hrtime(true) - $start) / 1_000_000);
        $this->releaseQueries();

        $this->line('  Wall time:      ' . $elapsedMs . ' ms');
        $this->printQuerySummary();
    }

    private function profileMetricsByProgram(
        StaffDashboardService $dashboardService,
        Program $program,
    ): void {
        $this->newLine();
        $this->info('── 3. Metric Collection: ' . $program->program_name . ' ─────');

        $currentYear = (int) now()->year;

        foreach ([$currentYear - 1, $currentYear] as $year) {
            $this->captureQueries();
            $start = hrtime(true);
            $metrics = $dashboardService->collectMetricsForSummary($program->id, $year, null);
            $elapsedMs = (int) ((hrtime(true) - $start) / 1_000_000);
            $this->releaseQueries();

            $this->line(sprintf('  %s (annual): %d ms, %d metrics, %d queries',
                $year, $elapsedMs, count($metrics), count($this->queryLog)));

            foreach ($this->groupQueriesByTable() as $table => $info) {
                $this->line(sprintf('    %-35s %d queries, %.1f ms avg', $table, $info['count'], $info['avg_time']));
            }
        }
    }

    private function captureQueries(): void
    {
        $this->queryLog = [];
        DB::enableQueryLog();

        DB::listen(function ($query) {
            $this->queryLog[] = [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
                'table' => $this->extractTable($query->sql),
            ];
        });
    }

    private function releaseQueries(): void
    {
        DB::disableQueryLog();
    }

    private function extractTable(string $sql): string
    {
        if (preg_match('/\bfrom\s+`?(\w+)`?/i', $sql, $m)) {
            return $m[1];
        }
        if (preg_match('/\bjoin\s+`?(\w+)`?/i', $sql, $m)) {
            return $m[1];
        }
        if (preg_match('/\bupdate\s+`?(\w+)`?/i', $sql, $m)) {
            return $m[1];
        }
        if (preg_match('/\binto\s+`?(\w+)`?/i', $sql, $m)) {
            return $m[1];
        }
        return 'unknown';
    }

    private function groupQueriesByTable(): array
    {
        $groups = [];

        foreach ($this->queryLog as $q) {
            $table = $q['table'];
            if (!isset($groups[$table])) {
                $groups[$table] = ['count' => 0, 'total_time' => 0.0];
            }
            $groups[$table]['count']++;
            $groups[$table]['total_time'] += $q['time'];
        }

        foreach ($groups as &$info) {
            $info['avg_time'] = $info['count'] > 0
                ? round($info['total_time'] / $info['count'], 2)
                : 0;
        }

        uasort($groups, fn($a, $b) => $b['total_time'] <=> $a['total_time']);

        return $groups;
    }

    private function printQuerySummary(): void
    {
        $totalQueries = count($this->queryLog);
        $totalDbTime = (int) collect($this->queryLog)->sum('time');
        $slowQueries = collect($this->queryLog)->sortByDesc('time')->take(5);

        $this->line('  Queries:        ' . $totalQueries);
        $this->line('  Total DB time:  ' . $totalDbTime . ' ms');

        if ($totalQueries > 0) {
            $this->line('');
            $this->line('  <fg=yellow>Top 5 slowest queries:</>');
            $index = 1;

            foreach ($slowQueries as $q) {
                $sql = Str::of($q['sql'])->limit(120);
                $this->line(sprintf('  %d. %s', $index, $sql));
                $bindings = json_encode($q['bindings']);
                $this->line(sprintf('     <fg=cyan>%.1f ms</> | <fg=green>%s</>', $q['time'], $q['table']));
                if ($bindings !== '[]' && $bindings !== '{}') {
                    $this->line(sprintf('     Bindings: %s', Str::limit($bindings, 100)));
                }
                $index++;
            }

            $this->line('');
            $this->line('  <fg=yellow>Query count by table:</>');
            foreach ($this->groupQueriesByTable() as $table => $info) {
                $ratio = $info['count'] / max(1, $totalQueries / 30);
                $bar = str_repeat('█', (int) min(30, $ratio));
                $this->line(sprintf('  %-30s %3d queries  %s  (avg %.1f ms)', $table, $info['count'], $bar, $info['avg_time']));
            }
        }
    }

    private function resolveType(): ReportType
    {
        $map = [
            'education' => ReportType::Education,
            'sports' => ReportType::Sports,
            'funding' => ReportType::Funding,
            'multi_section' => ReportType::OrganizationalOverview,
        ];

        return $map[$this->option('type')] ?? ReportType::Education;
    }

    private function resolvePeriodType(): ReportPeriod
    {
        if ($this->option('from') && $this->option('to')) {
            return ReportPeriod::MultiYear;
        }

        if ($this->option('quarter')) {
            return ReportPeriod::Quarterly;
        }

        return ReportPeriod::Annual;
    }

    private function buildPeriodConfig(ReportPeriod $periodType): array
    {
        $year = (int) ($this->option('year') ?? now()->year);

        return match ($periodType) {
            ReportPeriod::Quarterly => [
                'period_type' => 'quarterly',
                'year' => $year,
                'quarter' => (int) ($this->option('quarter') ?? 1),
            ],
            ReportPeriod::MultiYear => [
                'period_type' => 'multi_year',
                'from_year' => (int) ($this->option('from') ?? $year - 2),
                'to_year' => (int) ($this->option('to') ?? $year),
            ],
            default => [
                'period_type' => 'annual',
                'year' => $year,
            ],
        };
    }

    private function resolveDates(array $periodConfig): array
    {
        return match ($periodConfig['period_type']) {
            'quarterly' => [
                'start' => Carbon::create($periodConfig['year'], ($periodConfig['quarter'] - 1) * 3 + 1, 1)->startOfDay(),
                'end' => Carbon::create($periodConfig['year'], ($periodConfig['quarter'] - 1) * 3 + 3, 1)->endOfMonth()->endOfDay(),
            ],
            'annual' => [
                'start' => Carbon::create($periodConfig['year'], 1, 1)->startOfDay(),
                'end' => Carbon::create($periodConfig['year'], 12, 31)->endOfDay(),
            ],
            'multi_year' => [
                'start' => Carbon::create($periodConfig['from_year'], 1, 1)->startOfDay(),
                'end' => Carbon::create($periodConfig['to_year'], 12, 31)->endOfDay(),
            ],
            default => [
                'start' => now()->startOfYear(),
                'end' => now()->endOfDay(),
            ],
        };
    }

    private function resolveProgram(ReportType $type): ?Program
    {
        $name = $this->option('program');

        if ($name) {
            return Program::where('program_name', $name)->first();
        }

        if (in_array($type->value, [ReportType::OrganizationalOverview->value, ReportType::Funding->value], true)) {
            return null;
        }

        return match ($type) {
            ReportType::Education => Program::where('program_name', 'Education')->first(),
            ReportType::Sports => Program::where('program_name', 'Sports')->first(),
            default => null,
        };
    }
}
