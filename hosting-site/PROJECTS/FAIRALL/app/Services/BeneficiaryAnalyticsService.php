<?php

namespace App\Services;

use App\Models\Beneficiary;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class BeneficiaryAnalyticsService
{
    public function resolveFilters(array $input): array
    {
        $period = strtolower((string) Arr::get($input, 'period', 'year'));

        if (!in_array($period, ['week', 'month', 'year', 'custom'], true)) {
            $period = 'year';
        }

        $now = now();
        $academicYearInput = Arr::get($input, 'year');
        $selectedYear = $academicYearInput ? (int) $academicYearInput : (int) $now->year;
        $startDate = match ($period) {
            'week' => $now->copy()->subDays(6)->startOfDay(),
            'month' => $now->copy()->subMonth()->startOfDay(),
            'year' => Carbon::create($selectedYear, 1, 1)->startOfDay(),
            'custom' => $this->parseDate(Arr::get($input, 'from')) ?? $now->copy()->startOfMonth(),
            default => $now->copy()->startOfMonth(),
        };

        $endDate = match ($period) {
            'week' => $now->copy()->endOfDay(),
            'month' => $now->copy()->endOfDay(),
            'year' => Carbon::create($selectedYear, 12, 31)->endOfDay(),
            'custom' => $this->parseDate(Arr::get($input, 'to')) ?? $now->copy()->endOfDay(),
            default => $now->copy()->endOfMonth(),
        };

        if ($endDate->lt($startDate)) {
            [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
        }

        return [
            'period' => $period,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'academicYear' => $selectedYear,
            'isMultiYear' => $startDate->year !== $endDate->year,
        ];
    }

    public function compute(Beneficiary $beneficiary, array $filters, ?int $programId): array
    {
        $beneficiaryId = (int) $beneficiary->id;
        $startDate = $filters['startDate'];
        $endDate = $filters['endDate'];
        $isMultiYear = $filters['isMultiYear'];

        $metrics = [];
        $trends = [];

        if ($isMultiYear) {
            $trends['eqAttendance'] = $this->attendanceTrendByType($beneficiaryId, 'EQ Session', $startDate, $endDate);
            $trends['tutorialAttendance'] = $this->attendanceTrendByType($beneficiaryId, 'Tutorial Session', $startDate, $endDate);
            $trends['trainingAttendance'] = $this->attendanceTrendByType($beneficiaryId, 'Training Session', $startDate, $endDate);
            $trends['gwa'] = $this->gwaTrend($beneficiaryId, $startDate, $endDate);
            $trends['socioEmotional'] = $this->socioEmotionalTrend($beneficiaryId, $startDate, $endDate);
            $trends['uniqueVisits'] = $this->uniqueVisitsTrend($beneficiaryId, $startDate, $endDate);
        } else {
            $metrics['eqAttendance'] = $this->averageAttendanceByType($beneficiaryId, 'EQ Session', $startDate, $endDate);
            $metrics['tutorialAttendance'] = $this->averageAttendanceByType($beneficiaryId, 'Tutorial Session', $startDate, $endDate);
            $metrics['trainingAttendance'] = $this->averageAttendanceByType($beneficiaryId, 'Training Session', $startDate, $endDate);
            $metrics['gwa'] = $this->averageGwa($beneficiaryId, $startDate, $endDate);
            $metrics['socioEmotional'] = $this->averageSocioEmotional($beneficiaryId, $startDate, $endDate);
            $metrics['uniqueVisits'] = $this->uniqueVisitsCount($beneficiaryId, $startDate, $endDate);
        }

        return [
            'isMultiYear' => $isMultiYear,
            'metrics' => $metrics,
            'trends' => $trends,
        ];
    }

    public function averageAttendanceByType(int $beneficiaryId, string $activityType, Carbon $startDate, Carbon $endDate): ?float
    {
        $result = DB::table('attendances')
            ->join('activity_sessions', 'activity_sessions.id', '=', 'attendances.activity_session_id')
            ->join('activities', 'activities.id', '=', 'activity_sessions.activity_id')
            ->join('activity_types', 'activity_types.id', '=', 'activities.activity_type_id')
            ->where('attendances.beneficiary_id', $beneficiaryId)
            ->where('activity_types.name', $activityType)
            ->whereBetween(DB::raw('DATE(activity_sessions.schedule)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN attendances.attendance_status = ? THEN 1 ELSE 0 END) as present', ['present'])
            ->first();

        if (!$result || (int) $result->total === 0) {
            return null;
        }

        return round(((int) $result->present / (int) $result->total) * 100, 2);
    }

    public function attendanceTrendByType(int $beneficiaryId, string $activityType, Carbon $startDate, Carbon $endDate): array
    {
        $labels = $this->yearLabelsInRange($startDate, $endDate);

        if ($labels === []) {
            return ['labels' => [], 'data' => []];
        }

        $firstYear = (int) $labels[0];
        $lastYear = (int) $labels[count($labels) - 1];
        $data = array_fill(0, count($labels), null);

        $results = DB::table('attendances')
            ->join('activity_sessions', 'activity_sessions.id', '=', 'attendances.activity_session_id')
            ->join('activities', 'activities.id', '=', 'activity_sessions.activity_id')
            ->join('activity_types', 'activity_types.id', '=', 'activities.activity_type_id')
            ->where('attendances.beneficiary_id', $beneficiaryId)
            ->where('activity_types.name', $activityType)
            ->whereYear('activity_sessions.schedule', '>=', $firstYear)
            ->whereYear('activity_sessions.schedule', '<=', $lastYear)
            ->groupBy(DB::raw('YEAR(activity_sessions.schedule)'))
            ->selectRaw('YEAR(activity_sessions.schedule) as year, COUNT(*) as total, SUM(CASE WHEN attendances.attendance_status = ? THEN 1 ELSE 0 END) as present', ['present'])
            ->get()
            ->keyBy('year');

        foreach ($labels as $index => $yearLabel) {
            $row = $results->get((int) $yearLabel);
            if ($row && (int) $row->total > 0) {
                $data[$index] = round(((int) $row->present / (int) $row->total) * 100, 2);
            }
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public function averageGwa(int $beneficiaryId, Carbon $startDate, Carbon $endDate): ?float
    {
        $result = DB::table('academic_records')
            ->join('education_enrollments', 'education_enrollments.id', '=', 'academic_records.education_enrollment_id')
            ->where('academic_records.beneficiary_id', $beneficiaryId)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('education_enrollments.academic_year_start_date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->orWhereBetween('education_enrollments.academic_year_end_date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('education_enrollments.academic_year_start_date', '<=', $startDate->toDateString())
                            ->where('education_enrollments.academic_year_end_date', '>=', $endDate->toDateString());
                    });
            })
            ->selectRaw('AVG(academic_records.gwa) as avg_gwa')
            ->first();

        if (!$result || $result->avg_gwa === null) {
            return null;
        }

        return round((float) $result->avg_gwa, 2);
    }

    public function gwaTrend(int $beneficiaryId, Carbon $startDate, Carbon $endDate): array
    {
        $years = $this->yearLabelsInRange($startDate, $endDate);

        $labels = array_map(
            fn($year) => "{$year}-" . ((int) $year + 1),
            $years
        );

        if ($labels === []) {
            return ['labels' => [], 'data' => []];
        }

        $firstYear = (int) $years[0];
        $lastYear = (int) $years[count($years) - 1];
        $data = array_fill(0, count($labels), null);

        $results = DB::table('academic_records')
            ->join('education_enrollments', 'education_enrollments.id', '=', 'academic_records.education_enrollment_id')
            ->where('academic_records.beneficiary_id', $beneficiaryId)
            ->whereYear('education_enrollments.academic_year_start_date', '>=', $firstYear)
            ->whereYear('education_enrollments.academic_year_start_date', '<=', $lastYear)
            ->groupBy(DB::raw('YEAR(education_enrollments.academic_year_start_date)'))
            ->selectRaw('YEAR(education_enrollments.academic_year_start_date) as year, AVG(academic_records.gwa) as avg_gwa')
            ->get()
            ->keyBy('year');

        foreach ($years as $index => $year) {
            $row = $results->get((int) $year);
            if ($row && $row->avg_gwa !== null) {
                $data[$index] = round((float) $row->avg_gwa, 2);
            }
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public function averageSocioEmotional(int $beneficiaryId, Carbon $startDate, Carbon $endDate): ?float
    {
        $result = DB::table('ffa_assessment_records')
            ->join('assessment_categories', 'assessment_categories.id', '=', 'ffa_assessment_records.assessment_category_id')
            ->where('ffa_assessment_records.beneficiary_id', $beneficiaryId)
            ->where('assessment_categories.assessment_name', 'Socio-Emotional')
            ->whereBetween(DB::raw('DATE(ffa_assessment_records.date)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('AVG(ffa_assessment_records.score / NULLIF(ffa_assessment_records.max_score, 0) * 100) as avg_pct')
            ->first();

        if (!$result || $result->avg_pct === null) {
            return null;
        }

        return round((float) $result->avg_pct, 2);
    }

    public function socioEmotionalTrend(int $beneficiaryId, Carbon $startDate, Carbon $endDate): array
    {
        $labels = $this->yearLabelsInRange($startDate, $endDate);

        if ($labels === []) {
            return ['labels' => [], 'data' => []];
        }

        $firstYear = (int) $labels[0];
        $lastYear = (int) $labels[count($labels) - 1];
        $data = array_fill(0, count($labels), null);

        $results = DB::table('ffa_assessment_records')
            ->join('assessment_categories', 'assessment_categories.id', '=', 'ffa_assessment_records.assessment_category_id')
            ->where('ffa_assessment_records.beneficiary_id', $beneficiaryId)
            ->where('assessment_categories.assessment_name', 'Socio-Emotional')
            ->whereYear('ffa_assessment_records.date', '>=', $firstYear)
            ->whereYear('ffa_assessment_records.date', '<=', $lastYear)
            ->groupBy(DB::raw('YEAR(ffa_assessment_records.date)'))
            ->selectRaw('YEAR(ffa_assessment_records.date) as year, AVG(ffa_assessment_records.score / NULLIF(ffa_assessment_records.max_score, 0) * 100) as avg_pct')
            ->get()
            ->keyBy('year');

        foreach ($labels as $index => $yearLabel) {
            $row = $results->get((int) $yearLabel);
            if ($row && $row->avg_pct !== null) {
                $data[$index] = round((float) $row->avg_pct, 2);
            }
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public function uniqueVisitsCount(int $beneficiaryId, Carbon $startDate, Carbon $endDate): int
    {
        return (int) DB::table('attendances')
            ->join('activity_sessions', 'activity_sessions.id', '=', 'attendances.activity_session_id')
            ->join('activities', 'activities.id', '=', 'activity_sessions.activity_id')
            ->join('activity_types', 'activity_types.id', '=', 'activities.activity_type_id')
            ->where('attendances.beneficiary_id', $beneficiaryId)
            ->where('activity_types.name', 'Training Session')
            ->where('attendances.attendance_status', 'present')
            ->whereBetween(DB::raw('DATE(activity_sessions.schedule)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('COUNT(DISTINCT activity_sessions.id) as visits')
            ->value('visits');
    }

    public function uniqueVisitsTrend(int $beneficiaryId, Carbon $startDate, Carbon $endDate): array
    {
        $labels = $this->yearLabelsInRange($startDate, $endDate);

        if ($labels === []) {
            return ['labels' => [], 'data' => []];
        }

        $firstYear = (int) $labels[0];
        $lastYear = (int) $labels[count($labels) - 1];
        $data = array_fill(0, count($labels), 0);

        $results = DB::table('attendances')
            ->join('activity_sessions', 'activity_sessions.id', '=', 'attendances.activity_session_id')
            ->join('activities', 'activities.id', '=', 'activity_sessions.activity_id')
            ->join('activity_types', 'activity_types.id', '=', 'activities.activity_type_id')
            ->where('attendances.beneficiary_id', $beneficiaryId)
            ->where('activity_types.name', 'Training Session')
            ->where('attendances.attendance_status', 'present')
            ->whereYear('activity_sessions.schedule', '>=', $firstYear)
            ->whereYear('activity_sessions.schedule', '<=', $lastYear)
            ->groupBy(DB::raw('YEAR(activity_sessions.schedule)'))
            ->selectRaw('YEAR(activity_sessions.schedule) as year, COUNT(DISTINCT activity_sessions.id) as visits')
            ->get()
            ->keyBy('year');

        foreach ($labels as $index => $yearLabel) {
            $row = $results->get((int) $yearLabel);
            if ($row) {
                $data[$index] = (int) $row->visits;
            }
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function parseDate(mixed $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse((string) $value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    private function yearLabelsInRange(Carbon $startDate, Carbon $endDate): array
    {
        $years = [];

        for ($year = $startDate->year; $year <= $endDate->year; $year++) {
            $years[] = (string) $year;
        }

        return $years;
    }
}
