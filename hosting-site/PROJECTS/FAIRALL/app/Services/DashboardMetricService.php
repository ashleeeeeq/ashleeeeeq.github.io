<?php

namespace App\Services;

use App\Models\DashboardMetric;
use Carbon\Carbon;

class DashboardMetricService
{
    const DEFAULT_FRESHNESS_HOURS = 3;

    public function getOrCompute(
        int $programId,
        string $metric,
        ?string $dimension,
        int $year,
        ?int $month,
        \Closure $computation,
        ?int $freshnessHours = null,
    ): float {
        $cached = $this->get($programId, $metric, $dimension, $year, $month, $freshnessHours);
        if ($cached !== null) {
            return $cached;
        }

        $value = (float) $computation();
        $this->set($programId, $metric, $dimension, $year, $month, $value);

        return $value;
    }

    public function get(
        int $programId,
        string $metric,
        ?string $dimension,
        int $year,
        ?int $month,
        ?int $freshnessHours = null,
    ): ?float {
        $freshnessHours ??= self::DEFAULT_FRESHNESS_HOURS;
        $threshold = Carbon::now()->subHours($freshnessHours);

        $row = DashboardMetric::where('program_id', $programId)
            ->where('metric', $metric)
            ->where('year', $year)
            ->where('month', $month)
            ->whereNull('dimension')
            ->where('computed_at', '>=', $threshold)
            ->first();

        return $row ? (float) $row->value : null;
    }

    public function set(
        int $programId,
        string $metric,
        ?string $dimension,
        int $year,
        ?int $month,
        float $value,
    ): void {
        DashboardMetric::updateOrCreate(
            [
                'program_id' => $programId,
                'metric' => $metric,
                'dimension' => $dimension,
                'year' => $year,
                'month' => $month,
            ],
            [
                'value' => $value,
                'computed_at' => now(),
            ],
        );
    }

    public function getSet(
        int $programId,
        string $metric,
        int $year,
        ?int $month,
        array $dimensions,
        ?int $freshnessHours = null,
    ): ?array {
        $freshnessHours ??= self::DEFAULT_FRESHNESS_HOURS;
        $threshold = Carbon::now()->subHours($freshnessHours);

        $rows = DashboardMetric::where('program_id', $programId)
            ->where('metric', $metric)
            ->where('year', $year)
            ->where('month', $month)
            ->whereIn('dimension', $dimensions)
            ->where('computed_at', '>=', $threshold)
            ->get()
            ->keyBy('dimension');

        foreach ($dimensions as $dim) {
            if (!isset($rows[$dim])) {
                return null;
            }
        }

        return $rows->map(fn($r) => (float) $r->value)->all();
    }

    public function setSet(
        int $programId,
        string $metric,
        int $year,
        ?int $month,
        array $values,
    ): void {
        $now = now();
        $records = [];

        foreach ($values as $dimension => $value) {
            $records[] = [
                'program_id' => $programId,
                'metric' => $metric,
                'dimension' => $dimension,
                'year' => $year,
                'month' => $month,
                'value' => $value,
                'computed_at' => $now,
            ];
        }

        DashboardMetric::upsert(
            $records,
            ['program_id', 'year', 'month', 'metric', 'dimension'],
            ['value', 'computed_at'],
        );
    }

    public function bulkUpsert(array $metrics): void
    {
        $now = now();
        $records = [];

        foreach ($metrics as $m) {
            $records[] = [
                'program_id' => $m['program_id'],
                'metric' => $m['metric'],
                'dimension' => $m['dimension'] ?? null,
                'year' => $m['year'],
                'month' => $m['month'] ?? null,
                'value' => $m['value'],
                'computed_at' => $now,
            ];
        }

        if ($records !== []) {
            DashboardMetric::upsert(
                $records,
                ['program_id', 'year', 'month', 'metric', 'dimension'],
                ['value', 'computed_at'],
            );
        }
    }
}
