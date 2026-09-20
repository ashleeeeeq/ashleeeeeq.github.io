<?php

namespace App\Services\Reports;

use App\Enums\ReportType;
use App\Models\Program;
use App\Models\User;
use App\Services\StaffDashboardService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ReportGenerationService
{
    public function boot(ReportType $type, array $periodConfig, User $user): array
    {
        $dates = $this->resolveDates($periodConfig);
        $program = $this->resolveProgram($type, $user);
        $cacheKey = $this->cacheKey($type, $dates['start'], $dates['end'], $program);

        return Cache::remember($cacheKey, now()->addHours(3), function () use ($type, $dates, $program, $periodConfig) {
            $dashboardService = app(StaffDashboardService::class);

            return $dashboardService->buildReportPayload(
                $type,
                $dates['start'],
                $dates['end'],
                $program,
                $periodConfig['period_type']
            );
        });
    }

    private function resolveDates(array $periodConfig): array
    {
        $periodType = $periodConfig['period_type'];

        return match ($periodType) {
            'quarterly' => $this->quarterlyDates(
                (int) ($periodConfig['year'] ?? date('Y')),
                (int) ($periodConfig['quarter'] ?? 1)
            ),
            'annual' => $this->annualDates((int) ($periodConfig['year'] ?? date('Y'))),
            'multi_year' => [
                'start' => Carbon::create((int) ($periodConfig['from_year'] ?? date('Y')), 1, 1)->startOfDay(),
                'end' => Carbon::create((int) ($periodConfig['to_year'] ?? date('Y')), 12, 31)->endOfDay(),
            ],
            default => [
                'start' => now()->startOfYear(),
                'end' => now()->endOfDay(),
            ],
        };
    }

    private function quarterlyDates(int $year, int $quarter): array
    {
        $month = ($quarter - 1) * 3 + 1;

        return [
            'start' => Carbon::create($year, $month, 1)->startOfDay(),
            'end' => Carbon::create($year, $month + 2, 1)->endOfMonth()->endOfDay(),
        ];
    }

    private function annualDates(int $year): array
    {
        return [
            'start' => Carbon::create($year, 1, 1)->startOfDay(),
            'end' => Carbon::create($year, 12, 31)->endOfDay(),
        ];
    }

    private function resolveProgram(ReportType $type, User $user): ?Program
    {
        if ($type === ReportType::OrganizationalOverview || $type === ReportType::Funding) {
            return null;
        }

        $staff = $user->staff;

        if (!$staff) {
            return null;
        }

        if ($staff->hasAnyRole([
            \App\Models\Staff::ROLE_ADMINISTRATOR,
            \App\Models\Staff::ROLE_EXECUTIVE_DIRECTOR,
        ])) {
            return null;
        }

        return $staff->staffProgram();
    }

    private function cacheKey(ReportType $type, Carbon $startDate, Carbon $endDate, ?Program $program): string
    {
        return 'report.boot.' . implode('.', [
            $type->value,
            $startDate->toDateString(),
            $endDate->toDateString(),
            $program?->id ?? 'all',
        ]);
    }
}
