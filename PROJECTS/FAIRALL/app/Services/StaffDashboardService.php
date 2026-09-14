<?php

namespace App\Services;

use App\Enums\ReportType;
use App\Models\Program;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StaffDashboardService
{
    private ?int $cachedEducationProgramId = null;
    private ?int $cachedSportsProgramId = null;
    private ?int $cachedSocioEmotionalCategoryId = null;
    private array $cachedProgramEnrollments = [];
    private array $cachedEducationBreakdownsByYears = [];

    public function build(User $user, array $input = [], ?string $activeSection = null): array
    {
        $user->loadMissing(['staff.department', 'staff.position']);

        $staff = $user->staff;
        $filters = $this->resolveFilters($input);
        $requestedSection = in_array($activeSection ?? Arr::get($input, 'section', 'beneficiary'), ['beneficiary', 'activity', 'donor'], true)
            ? ($activeSection ?? Arr::get($input, 'section', 'beneficiary'))
            : 'beneficiary';

        if (!$staff) {
            return [
                'name' => $user->display_name,
                'role' => null,
                'department' => null,
                'position' => null,
                'userType' => $user->user_type,
                'showProgramFilter' => false,
                'programOptions' => Cache::remember('dashboard.program-options', now()->addHours(6), fn() => []),
                'selectedProgramId' => null,
                'filterOptions' => Cache::remember('dashboard.filter-options', now()->addHours(6), fn() => $this->buildFilterOptions()),
                'selectedProgramName' => null,
                'filters' => $filters,
                'sectionVisibility' => $this->buildSectionVisibility(null),
                'beneficiaryMetrics' => [],
                'activityMetrics' => [],
                'donorMetrics' => [],
                'activeSection' => $requestedSection,
            ];
        }

        $programScope = $this->resolveProgramScope($staff, $input);
        $programOptions = Cache::remember('dashboard.program-options', now()->addHours(6), function () {
            return Program::query()
                ->orderBy('program_name')
                ->get(['id', 'program_name'])
                ->map(fn(Program $program) => [
                    'id' => $program->id,
                    'name' => $program->program_name,
                ])
                ->all();
        });

        $cacheKey = $this->dashboardCacheKey($user, $staff, $filters, $programScope, $requestedSection);

        return Cache::remember($cacheKey, now()->addSeconds(90), function () use ($user, $staff, $filters, $programOptions, $programScope, $requestedSection) {
            $payload = [
                'name' => $user->display_name,
                'role' => $staff->role,
                'department' => $staff->department?->name,
                'position' => $staff->position?->name,
                'userType' => $user->user_type,
                'showProgramFilter' => $this->canUseProgramFilter($staff),
                'programOptions' => $programOptions,
                'filterOptions' => Cache::remember('dashboard.filter-options', now()->addHours(6), fn() => $this->buildFilterOptions()),
                'selectedProgramId' => $programScope?->id,
                'selectedProgramName' => $programScope?->program_name,
                'filters' => $filters,
                'sectionVisibility' => $this->buildSectionVisibility($staff),
                'beneficiaryMetrics' => [],
                'activityMetrics' => [],
                'donorMetrics' => [],
                'activeSection' => $requestedSection,
            ];

            if ($requestedSection === 'beneficiary' && ($payload['sectionVisibility']['beneficiary'] ?? false)) {
                $payload['beneficiaryMetrics'] = $this->buildBeneficiaryMetrics($filters, $programScope);
            }

            if ($requestedSection === 'activity' && ($payload['sectionVisibility']['activity'] ?? false)) {
                $payload['activityMetrics'] = $this->buildActivityMetrics($filters, $programScope);
            }

            if ($requestedSection === 'donor' && ($payload['sectionVisibility']['donor'] ?? false)) {
                $payload['donorMetrics'] = $this->buildDonorMetrics($filters, $staff, $programScope);
            }

            return $payload;
        });
    }

    private function dashboardCacheKey(User $user, Staff $staff, array $filters, ?Program $programScope, string $section): string
    {
        return 'dashboard.payload.' . implode('.', [
            $user->id,
            $staff->id,
            $section,
            $programScope?->id ?? 'all',
            md5(json_encode($filters)),
        ]);
    }

    private function buildSectionVisibility(?Staff $staff): array
    {
        if (!$staff) {
            return [
                'beneficiary' => false,
                'activity' => false,
                'donor' => false,
            ];
        }

        $department = Str::lower(trim((string) $staff->department?->name));
        $elevated = $this->canUseProgramFilter($staff);

        return [
            'beneficiary' => $elevated || in_array($department, ['education', 'sports'], true),
            'activity' => $elevated || in_array($department, ['education', 'sports'], true),
            'donor' => $elevated || $staff->hasAnyRole([Staff::ROLE_DONOR_MANAGER, Staff::ROLE_ADMIN_FINANCE_STAFF]),
        ];
    }

    private function canUseProgramFilter(Staff $staff): bool
    {
        return $staff->hasAnyRole([Staff::ROLE_ADMINISTRATOR, Staff::ROLE_EXECUTIVE_DIRECTOR]);
    }

    private function resolveProgramScope(Staff $staff, array $input): ?Program
    {
        if (!$this->canUseProgramFilter($staff)) {
            return $staff->staffProgram();
        }

        $programId = Arr::get($input, 'program_id');

        if (!$programId) {
            return null;
        }

        return Program::query()->find($programId);
    }

    private function programMatchesScope(?Program $programScope, string $targetProgramName): bool
    {
        if (!$programScope) {
            return true;
        }

        return Str::lower(trim((string) $programScope->program_name)) === Str::lower(trim($targetProgramName));
    }

    private function resolveFilters(array $input): array
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
            'year' => $selectedYear === (int) $now->year
                ? $now->copy()->endOfDay()
                : Carbon::create($selectedYear, 12, 31)->endOfDay(),
            'custom' => $this->parseDate(Arr::get($input, 'to')) ?? $now->copy()->endOfDay(),
            default => $now->copy()->endOfMonth(),
        };

        if ($endDate->lt($startDate)) {
            [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
        }

        $academicYear = Arr::get($input, 'year');
        $gradeLevel = Arr::get($input, 'grade_level');
        $sportTypeId = Arr::get($input, 'sport_type_id');
        $ageGroup = Arr::get($input, 'age_group');

        return [
            'period' => $period,
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'academicYear' => $academicYear ? (int) $academicYear : ($period === 'custom' ? (int) $startDate->year : (int) $now->year),
            'programId' => Arr::get($input, 'program_id'),
            'gradeLevel' => is_string($gradeLevel) && $gradeLevel !== '' ? $gradeLevel : null,
            'sportTypeId' => is_numeric($sportTypeId) ? (int) $sportTypeId : null,
            'ageGroup' => is_string($ageGroup) && $ageGroup !== '' ? $ageGroup : null,
        ];
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

    private function dateRangeBounds(Carbon $startDate, Carbon $endDate): array
    {
        return [
            $startDate->copy()->startOfDay(),
            $endDate->copy()->endOfDay(),
        ];
    }

    private function yearRangeBounds(int $year): array
    {
        return [
            Carbon::create($year, 1, 1)->startOfDay(),
            Carbon::create($year, 12, 31)->endOfDay(),
        ];
    }

    private function educationBreakdownsByYears(int $programId, array $academicYears): array
    {
        $years = array_values(array_unique(array_map('intval', $academicYears)));
        sort($years);

        if ($years === []) {
            return ['transition' => [], 'completion' => []];
        }

        $cacheKey = $programId . ':' . implode(',', $years);
        if (isset($this->cachedEducationBreakdownsByYears[$cacheKey])) {
            return $this->cachedEducationBreakdownsByYears[$cacheKey];
        }

        $yearSet = array_fill_keys($years, true);
        $transition = [];
        $completion = [];

        foreach ($years as $year) {
            $transition[$year] = [
                'elementary' => ['total' => 0, 'success' => 0],
                'high_school' => ['total' => 0, 'success' => 0],
                'shs' => ['total' => 0, 'success' => 0],
            ];

            $completion[$year] = [
                'elementary' => ['total' => 0, 'completed' => 0],
                'high_school' => ['total' => 0, 'completed' => 0],
                'shs' => ['total' => 0, 'completed' => 0],
                'college' => ['total' => 0, 'completed' => 0],
            ];
        }

        $expectedTransitions = [
            'elementary' => 'high_school',
            'high_school' => 'shs',
            'shs' => 'college',
        ];

        $enrollmentsByBeneficiary = $this->programEducationEnrollments($programId);

        foreach ($enrollmentsByBeneficiary as $beneficiaryHistory) {
            $latestCollegeEnrollment = null;

            foreach ($beneficiaryHistory as $index => $currentEnrollment) {
                $academicYear = (int) substr($currentEnrollment->academic_year_start_date, 0, 4);
                $stage = $this->stageFromGradeLevel((string) $currentEnrollment->grade_level);
                $normalizedStage = $this->normalizeEducationLevelStage($currentEnrollment->education_level);

                if ($normalizedStage === 'college') {
                    $latestCollegeEnrollment = $currentEnrollment;
                }

                if (!isset($yearSet[$academicYear])) {
                    continue;
                }

                $terminalGrade = $stage ? $this->terminalGradeForStage($stage) : null;

                if (!$stage || $normalizedStage !== $stage || !$terminalGrade || (string) $currentEnrollment->grade_level !== $terminalGrade) {
                    continue;
                }

                if (isset($transition[$academicYear][$stage]) && $currentEnrollment->enrollment_status === 'completed') {
                    $transition[$academicYear][$stage]['total']++;

                    $nextEnrollment = $beneficiaryHistory[$index + 1] ?? null;
                    if ($nextEnrollment && $this->normalizeEducationLevelStage($nextEnrollment->education_level) === ($expectedTransitions[$stage] ?? null)) {
                        $transition[$academicYear][$stage]['success']++;
                    }
                }

                if (isset($completion[$academicYear][$stage])) {
                    $completion[$academicYear][$stage]['total']++;

                    if ($currentEnrollment->enrollment_status === 'completed') {
                        $completion[$academicYear][$stage]['completed']++;
                    }
                }
            }

            if (!$latestCollegeEnrollment || $latestCollegeEnrollment->enrollment_status === 'active') {
                continue;
            }

            $academicYear = (int) substr($latestCollegeEnrollment->academic_year_start_date, 0, 4);

            if (!isset($yearSet[$academicYear])) {
                continue;
            }

            if (!in_array(Str::lower(trim((string) $latestCollegeEnrollment->grade_level)), ['4th year', '5th year'], true)) {
                continue;
            }

            $completion[$academicYear]['college']['total']++;

            if ($latestCollegeEnrollment->enrollment_status === 'completed') {
                $completion[$academicYear]['college']['completed']++;
            }
        }

        return $this->cachedEducationBreakdownsByYears[$cacheKey] = [
            'transition' => $transition,
            'completion' => $completion,
        ];
    }

    private function programIdByName(string $programName): ?int
    {
        return Program::query()
            ->whereRaw('LOWER(program_name) = ?', [Str::lower($programName)])
            ->value('id');
    }

    private function buildBeneficiaryMetrics(array $filters, ?Program $programScope): array
    {
        $startDate = Carbon::parse($filters['startDate']);
        $endDate = Carbon::parse($filters['endDate']);
        $educationProgramId = $this->educationProgramId();
        $sportsProgramId = $this->sportsProgramId();
        $socioEmotionalCategoryId = $this->socioEmotionalCategoryId();
        $scopeEducation = $this->programMatchesScope($programScope, 'education');
        $scopeSports = $this->programMatchesScope($programScope, 'sports');

        if ($scopeEducation || $scopeSports) {
            $statusCounts = $this->activeStatusCounts($educationProgramId, $sportsProgramId, $endDate);
        }
        $scholarStageCounts = $scopeEducation
            ? $this->resolveMetricLabelsData(
                'active_scholars',
                $educationProgramId,
                $startDate,
                $endDate,
                ['Elementary', 'High School', 'SHS', 'College'],
                fn() => $this->scholarCountsByEducationalStage($educationProgramId, $startDate, $endDate),
            )
            : ['labels' => [], 'data' => []];
        $scholars = $scopeEducation ? array_sum($scholarStageCounts['data']) : 0;
        $registeredPlayers = $scopeSports ? ($statusCounts['registeredPlayers'] ?? 0) : 0;

        $yearsInRange = $this->yearLabelsInRange($startDate, $endDate);

        if (count($yearsInRange) > 1) {
            $aggregatedTransitionSuccess = [];
            $aggregatedTransitionTotal = [];
            $aggregatedCompletionCompleted = [];
            $aggregatedCompletionTotal = [];
            $educationBreakdowns = $scopeEducation
                ? $this->educationBreakdownsByYears($educationProgramId, $yearsInRange)
                : ['transition' => [], 'completion' => []];

            $aggregatedGraduates = $scopeEducation
                ? $this->resolveMetricKeyedSet(
                    'graduates',
                    $educationProgramId,
                    $startDate,
                    $endDate,
                    ['elementary', 'high_school', 'shs', 'college'],
                    fn() => $this->graduatesByStageAggregate($educationProgramId, $startDate, $endDate),
                )
                : [];

            foreach ($yearsInRange as $year) {
                $yearTransition = $educationBreakdowns['transition'][(int) $year] ?? null;
                if ($yearTransition) {
                    foreach ($yearTransition as $stage => $values) {
                        $aggregatedTransitionSuccess[$stage] = ($aggregatedTransitionSuccess[$stage] ?? 0) + ($values['success'] ?? 0);
                        $aggregatedTransitionTotal[$stage] = ($aggregatedTransitionTotal[$stage] ?? 0) + ($values['total'] ?? 0);
                    }
                }

                $yearCompletion = $educationBreakdowns['completion'][(int) $year] ?? null;
                if ($yearCompletion) {
                    foreach ($yearCompletion as $stage => $values) {
                        $aggregatedCompletionCompleted[$stage] = ($aggregatedCompletionCompleted[$stage] ?? 0) + ($values['completed'] ?? 0);
                        $aggregatedCompletionTotal[$stage] = ($aggregatedCompletionTotal[$stage] ?? 0) + ($values['total'] ?? 0);
                    }
                }
            }

            $graduatesByStage = $aggregatedGraduates;

            $aggregatedRates = [];
            foreach ($aggregatedTransitionTotal as $stage => $total) {
                if ($total > 0) {
                    $aggregatedRates[$stage] = round(($aggregatedTransitionSuccess[$stage] / $total) * 100, 2);
                }
            }
            $transitionBreakdown = [
                'rates' => $aggregatedRates,
                'successCounts' => $aggregatedTransitionSuccess,
                'totalCounts' => $aggregatedTransitionTotal,
                'academicYear' => $startDate->year,
            ];

            $aggregatedCompletionRates = [];
            foreach ($aggregatedCompletionTotal as $stage => $total) {
                if ($total > 0) {
                    $aggregatedCompletionRates[$stage] = round(($aggregatedCompletionCompleted[$stage] / $total) * 100, 2);
                }
            }
            $completionBreakdown = [
                'rates' => $aggregatedCompletionRates,
                'completedCounts' => $aggregatedCompletionCompleted,
                'totalCounts' => $aggregatedCompletionTotal,
                'academicYear' => $startDate->year,
            ];
        } else {
            $singleYearStart = Carbon::create((int) $filters['academicYear'], 1, 1)->startOfDay();
            $singleYearEnd = Carbon::create((int) $filters['academicYear'], 12, 31)->endOfDay();

            $graduatesByStage = $scopeEducation
                ? $this->resolveMetricKeyedSet(
                    'graduates',
                    $educationProgramId,
                    $singleYearStart,
                    $singleYearEnd,
                    ['elementary', 'high_school', 'shs', 'college'],
                    fn() => $this->graduatesByStage($educationProgramId, $filters['academicYear']),
                )
                : [];

            $transitionBreakdown = $scopeEducation
                ? $this->stageTransitionRateBreakdown($educationProgramId, $filters['academicYear'])
                : null;
            $completionBreakdown = $scopeEducation
                ? $this->stageCompletionRateBreakdown($educationProgramId, $filters['academicYear'])
                : null;
        }

        return [
            'scholars' => $scholars,
            'scholarsExited' => $scopeEducation
                ? ($this->coversCompleteYear($startDate, $endDate)
                    ? app(\App\Services\DashboardMetricService::class)->getOrCompute(
                        $educationProgramId, 'exited_scholars', null, $startDate->year, null,
                        fn() => (float) $this->scholarsExitedCount($educationProgramId, $startDate, $endDate),
                    )
                    : (float) $this->scholarsExitedCount($educationProgramId, $startDate, $endDate)
                )
                : 0,
            'registeredPlayers' => $registeredPlayers,
            'graduatesTotal' => array_sum($graduatesByStage),
            'graduatesByStage' => $graduatesByStage,
            'graduatesByStageByYear' => $scopeEducation ? $this->graduatesByStageByYear($educationProgramId, $startDate, $endDate) : ['labels' => [], 'datasets' => []],
            'competitionParticipation' => $this->competitionParticipationCount($filters, $programScope),
            'averageGradesByStage' => $scopeEducation ? $this->resolveMetricKeyedSet(
                'avg_grade',
                $educationProgramId,
                $startDate,
                $endDate,
                ['elementary', 'high_school', 'shs', 'college'],
                fn() => $this->averageGradesByStage($educationProgramId, $startDate, $endDate),
            ) : [],
            'averageSocioEmotionalScore' => $scopeEducation ? $this->averageSocioEmotionalScore($educationProgramId, $startDate, $endDate, $socioEmotionalCategoryId) : null,
            'averageSocioEmotionalByStage' => $scopeEducation ? $this->resolveMetricKeyedSet(
                'avg_socio_emotional',
                $educationProgramId,
                $startDate,
                $endDate,
                ['elementary', 'high_school', 'shs', 'college'],
                fn() => $this->averageSocioEmotionalByStage($educationProgramId, $startDate, $endDate, $socioEmotionalCategoryId),
            ) : [],
            'transitionRates' => $transitionBreakdown ? $transitionBreakdown['rates'] : [],
            'completionRates' => $completionBreakdown ? $completionBreakdown['rates'] : [],
            'transitionRateBreakdown' => $transitionBreakdown ?: ['rates' => [], 'successCounts' => [], 'totalCounts' => [], 'academicYear' => (int) $filters['academicYear']],
            'completionRateBreakdown' => $completionBreakdown ?: ['rates' => [], 'completedCounts' => [], 'totalCounts' => [], 'academicYear' => (int) $filters['academicYear']],
            'transitionRatesByAcademicYear' => $scopeEducation ? $this->transitionRatesByAcademicYear($educationProgramId, $startDate, $endDate) : ['labels' => [], 'datasets' => []],
            'completionRatesByAcademicYear' => $scopeEducation ? $this->completionRatesByAcademicYear($educationProgramId, $startDate, $endDate) : ['labels' => [], 'datasets' => []],
            'selectedDateRange' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],
            'scholarsByEducationalStage' => $scholarStageCounts,
            'scholarsByEducationalStageByYear' => $scopeEducation ? $this->scholarCountsByEducationalStageByYear($educationProgramId, $startDate, $endDate) : ['labels' => [], 'datasets' => []],
            'newPlayersBySportsType' => $scopeSports ? $this->newPlayerCountsBySportsType($sportsProgramId, $startDate, $endDate) : ['labels' => [], 'data' => []],
            'newPlayersBySportsTypeByYear' => $scopeSports ? $this->newPlayerCountsBySportsTypeByYear($sportsProgramId, $startDate, $endDate) : ['labels' => [], 'datasets' => []],
            'activePlayersBySportsType' => $scopeSports ? $this->activePlayersBySportsType($sportsProgramId, $startDate, $endDate) : ['labels' => [], 'data' => []],
            'activePlayersBySportsTypeByYear' => $scopeSports ? $this->activePlayersBySportsTypeByYear($sportsProgramId, $startDate, $endDate) : ['labels' => [], 'datasets' => []],
            'trends' => array_merge(
                $this->timeSeriesStatusCountsMerged(
                    $scopeEducation ? $educationProgramId : null,
                    $scopeSports ? $sportsProgramId : null,
                    $startDate, $endDate, $filters['period']
                ),
                ['stages' => $scopeEducation
                    ? $this->stageDistributionByAcademicYear($educationProgramId)
                    : ['labels' => [], 'datasets' => []]]
            ),
        ];
    }

    private function resolveTrendPeriod(Carbon $startDate, Carbon $endDate): string
    {
        $daysDiff = (int) $startDate->diffInDays($endDate);
        if ($daysDiff > 365) return 'yearly';
        if ($daysDiff <= 30) return 'daily';
        if ($daysDiff <= 60) return 'weekly';
        return 'monthly';
    }

    private function buildActivityMetrics(array $filters, ?Program $programScope): array
    {
        $startDate = Carbon::parse($filters['startDate']);
        $endDate = Carbon::parse($filters['endDate']);
        $gradeLevel = $filters['gradeLevel'];
        $sportTypeId = $filters['sportTypeId'];
        $ageGroup = $filters['ageGroup'];
        $educationProgramId = $this->educationProgramId();
        $sportsProgramId = $this->sportsProgramId();
        $scopeEducation = $this->programMatchesScope($programScope, 'education');
        $scopeSports = $this->programMatchesScope($programScope, 'sports');
        $trendPeriod = $this->resolveTrendPeriod($startDate, $endDate);

        $educationTimeSeries = $scopeEducation
            ? $this->educationAttendanceTimeSeries($educationProgramId, $startDate, $endDate, $trendPeriod, $gradeLevel)
            : null;

        $trainingByYear = $scopeSports
            ? $this->trainingTimeSeriesByYearMerged($sportsProgramId, $startDate, $endDate, $sportTypeId, $ageGroup)
            : null;

        $trainingTrends = $scopeSports
            ? $this->trainingTimeSeriesMerged($sportsProgramId, $startDate, $endDate, $trendPeriod, $sportTypeId, $ageGroup)
            : null;

        $educationYearly = $educationTimeSeries['yearlyAggregates'] ?? null;

        return [
            'eqSessionAttendance' => $educationTimeSeries ? $educationTimeSeries['averages']['eq_session'] : null,
            'tutorialSessionAttendance' => $educationTimeSeries ? $educationTimeSeries['averages']['tutorial_session'] : null,
            'trainingSessionAttendance' => $trainingTrends ? $trainingTrends['average'] : null,
            'uniqueVisits' => $trainingTrends ? array_sum($trainingTrends['uniqueVisits']['data']) : 0,
            'attendanceTrendsByYear' => array_merge(
                [
                    'eqSessionAttendance' => $educationYearly['eqSessionAttendance'] ?? ['labels' => [], 'data' => []],
                    'tutorialSessionAttendance' => $educationYearly['tutorialSessionAttendance'] ?? ['labels' => [], 'data' => []],
                ],
                $trainingByYear
                    ? ['trainingSessionAttendance' => $trainingByYear['trainingSessionAttendance']]
                    : ['trainingSessionAttendance' => ['labels' => [], 'data' => []]]
            ),
            'uniqueVisitsByYear' => $trainingByYear ? $trainingByYear['uniqueVisitsByYear'] : ['labels' => [], 'data' => []],
            'trends' => array_merge(
                [
                    'eqSessionAttendance' => $educationTimeSeries ? $educationTimeSeries['eqSessionAttendance'] : ['labels' => [], 'data' => []],
                    'tutorialSessionAttendance' => $educationTimeSeries ? $educationTimeSeries['tutorialSessionAttendance'] : ['labels' => [], 'data' => []],
                ],
                $trainingTrends
                    ? ['trainingSessionAttendance' => $trainingTrends['trainingSessionAttendance'], 'uniqueVisits' => $trainingTrends['uniqueVisits']]
                    : ['trainingSessionAttendance' => ['labels' => [], 'data' => []], 'uniqueVisits' => ['labels' => [], 'data' => []]]
            ),
        ];
    }


    /**
     * Fill bucket data by mapping grouped query results into the pre-defined label array.
     */
    private function fillBucketData(array $labels, \Illuminate\Support\Collection $results, string $keyColumn, string $valueColumn, string $labelKeyPrefix = ''): array
    {
        $data = array_fill(0, count($labels), 0);
        $keyed = $results->keyBy($keyColumn);

        foreach ($labels as $index => $label) {
            // Try exact match first, then with prefix
            $lookupKey = $labelKeyPrefix ? ($labelKeyPrefix . substr($label, strlen($labelKeyPrefix))) : $label;
            $row = $keyed->get($lookupKey ?? $label) ?? $keyed->get($label);

            if ($row) {
                $data[$index] = (int) $row->{$valueColumn};
            }
        }

        return $data;
    }

    /**
     * Generate date labels and build a lookup map from grouped query results.
     * Returns ['labels' => [...], 'data' => [...]].
     */
    private function timeSeriesFromBuckets(\Closure $queryBuilder, Carbon $startDate, Carbon $endDate, string $period): array
    {
        // Generate the bucket structure
        $labels = [];
        $cursor = $startDate->copy()->startOfDay();

        if ($period === 'year' || $period === 'monthly') {
            while ($cursor->lte($endDate)) {
                $labels[] = $cursor->format('Y-m');
                $cursor->addMonth();
            }
        } elseif ($period === 'yearly') {
            while ($cursor->lte($endDate)) {
                $labels[] = (string) $cursor->year;
                $cursor->addYear();
            }
        } elseif ($period === 'weekly') {
            while ($cursor->lte($endDate)) {
                $weekStart = $cursor->copy()->startOfWeek();
                $labels[] = $weekStart->format('M d');
                $cursor = $weekStart->addWeek();
            }
        } else {
            while ($cursor->lte($endDate)) {
                $labels[] = $cursor->format('Y-m-d');
                $cursor->addDay();
            }
        }

        // Run a single aggregated query via the callback
        $results = $queryBuilder($startDate, $endDate, $period, $labels);

        // Build the lookup map from query results
        $lookup = [];
        $data = array_fill(0, count($labels), 0);

        foreach ($results as $row) {
            // The row should have 'bucket_label' and 'bucket_value' properties
            $lookup[$row->bucket_label] = (int) $row->bucket_value;
        }

        foreach ($labels as $index => $label) {
            if (isset($lookup[$label])) {
                $data[$index] = $lookup[$label];
            }
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Generate year labels and fill data from a single grouped query.
     * The callback receives ($startDate, $endDate) and should return rows
     * with 'year' and 'value' properties.
     */
    private function yearSeriesFromBuckets(\Closure $queryBuilder, Carbon $startDate, Carbon $endDate): array
    {
        $labels = $this->yearLabelsInRange($startDate, $endDate);

        if ($labels === []) {
            return ['labels' => [], 'data' => []];
        }

        $results = $queryBuilder($startDate, $endDate, $labels);

        $lookup = [];
        $data = array_fill(0, count($labels), 0);

        foreach ($results as $row) {
            $lookup[(int) $row->year] = (int) $row->value;
        }

        foreach ($labels as $index => $label) {
            $year = (int) $label;
            if (isset($lookup[$year])) {
                $data[$index] = $lookup[$year];
            }
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function generateBuckets(Carbon $start, Carbon $end, string $period): array
    {
        $labels = [];
        $ranges = [];

        $cursor = $start->copy()->startOfDay();

        if ($period === 'year' || $period === 'monthly') {
            while ($cursor->lte($end)) {
                $monthStart = $cursor->copy()->startOfMonth();
                $monthEnd = $monthStart->copy()->endOfMonth();
                $labels[] = $monthStart->format('Y-m');
                $ranges[] = ['start' => $monthStart->copy(), 'end' => $monthEnd->copy()];
                $cursor = $monthStart->addMonth();
            }
        } elseif ($period === 'yearly') {
            while ($cursor->lte($end)) {
                $yearStart = $cursor->copy()->startOfYear();
                $yearEnd = $yearStart->copy()->endOfYear();
                $labels[] = (string) $yearStart->year;
                $ranges[] = ['start' => $yearStart->copy(), 'end' => $yearEnd->copy()];
                $cursor = $yearStart->addYear();
            }
        } elseif ($period === 'weekly') {
            while ($cursor->lte($end)) {
                $weekStart = $cursor->copy()->startOfWeek();
                $weekEnd = $weekStart->copy()->endOfWeek();
                $labels[] = $weekStart->format('M d');
                $ranges[] = ['start' => $weekStart->copy(), 'end' => $weekEnd->copy()];
                $cursor = $weekStart->addWeek();
            }
        } else {
            while ($cursor->lte($end)) {
                $labels[] = $cursor->format('Y-m-d');
                $ranges[] = ['start' => $cursor->copy()->startOfDay(), 'end' => $cursor->copy()->endOfDay()];
                $cursor->addDay();
            }
        }

        return ['labels' => $labels, 'ranges' => $ranges];
    }

    private function timeSeriesStatusCountsMerged(
        ?int $educationProgramId,
        ?int $sportsProgramId,
        Carbon $startDate,
        Carbon $endDate,
        string $period
    ): array {
        $buckets = $this->generateBuckets($startDate, $endDate, $period);
        $scholarData = array_fill(0, count($buckets['labels']), 0);
        $playerData = array_fill(0, count($buckets['labels']), 0);

        $hasEducation = $educationProgramId !== null;
        $hasSports = $sportsProgramId !== null;

        if (!$hasEducation && !$hasSports) {
            return [
                'scholars' => ['labels' => $buckets['labels'], 'data' => $scholarData],
                'registeredPlayers' => ['labels' => $buckets['labels'], 'data' => $playerData],
            ];
        }

        $statusPeriods = DB::table('beneficiary_statuses')
            ->join('beneficiary_status_types', 'beneficiary_status_types.id', '=', 'beneficiary_statuses.beneficiary_status_type_id')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'beneficiary_statuses.beneficiary_id')
            ->where(function ($q) use ($educationProgramId, $sportsProgramId) {
                if ($educationProgramId) {
                    $q->orWhere(function ($q2) use ($educationProgramId) {
                        $q2->where('memberships.program_id', $educationProgramId)
                            ->whereRaw('LOWER(beneficiary_status_types.status_name) LIKE ?', ['%scholar%']);
                    });
                }
                if ($sportsProgramId) {
                    $q->orWhere(function ($q2) use ($sportsProgramId) {
                        $q2->where('memberships.program_id', $sportsProgramId)
                            ->whereRaw('LOWER(beneficiary_status_types.status_name) LIKE ?', ['%registered player%']);
                    });
                }
            })
            ->where('beneficiary_statuses.start_date', '<=', $endDate->toDateString())
            ->where(function ($q) use ($startDate) {
                $q->whereNull('beneficiary_statuses.end_date')
                    ->orWhere('beneficiary_statuses.end_date', '>=', $startDate->toDateString());
            })
            ->select(
                'beneficiary_statuses.beneficiary_id',
                'beneficiary_statuses.start_date',
                'beneficiary_statuses.end_date',
                'memberships.program_id',
                'beneficiary_status_types.status_name'
            )
            ->get();

        foreach ($statusPeriods as $status) {
            $isScholar = $hasEducation && (int) $status->program_id === $educationProgramId && stripos($status->status_name, 'scholar') !== false;
            $isPlayer = $hasSports && (int) $status->program_id === $sportsProgramId && stripos($status->status_name, 'registered player') !== false;

            if (!$isScholar && !$isPlayer) continue;

            $statusStart = $status->start_date;
            $statusEnd = $status->end_date ?? $endDate->toDateString();

            foreach ($buckets['ranges'] as $index => $range) {
                if ($statusStart <= $range['end']->toDateString() && $statusEnd >= $range['start']->toDateString()) {
                    if ($isScholar) $scholarData[$index]++;
                    else $playerData[$index]++;
                }
            }
        }

        return [
            'scholars' => ['labels' => $buckets['labels'], 'data' => $scholarData],
            'registeredPlayers' => ['labels' => $buckets['labels'], 'data' => $playerData],
        ];
    }

    private function stageDistributionByAcademicYear(?int $programId): array
    {
        if (!$programId) {
            return ['labels' => [], 'datasets' => []];
        }

        $rows = DB::table('academic_records')
            ->join('education_enrollments', 'education_enrollments.id', '=', 'academic_records.education_enrollment_id')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'academic_records.beneficiary_id')
            ->where('memberships.program_id', $programId)
            ->selectRaw(
                "CONCAT(YEAR(education_enrollments.academic_year_start_date), '-', YEAR(education_enrollments.academic_year_end_date)) as academic_year,
                " . $this->stageCaseSql('education_enrollments.grade_level') . " as stage,
                COUNT(DISTINCT academic_records.beneficiary_id) as total"
            )
            ->groupByRaw(
                "CONCAT(YEAR(education_enrollments.academic_year_start_date), '-', YEAR(education_enrollments.academic_year_end_date)),
                " . $this->stageCaseSql('education_enrollments.grade_level')
            )
            ->orderByRaw('academic_year ASC')
            ->get();

        $labels = $rows->pluck('academic_year')->unique()->values()->all();
        $stages = ['elementary', 'high_school', 'shs', 'college'];
        $datasets = [];

        foreach ($stages as $stage) {
            $series = array_fill(0, count($labels), 0);

            foreach ($rows->where('stage', $stage) as $row) {
                $index = array_search($row->academic_year, $labels, true);

                if ($index !== false) {
                    $series[$index] = (int) $row->total;
                }
            }

            $datasets[] = ['label' => Str::headline($stage), 'data' => $series];
        }

        return ['labels' => $labels, 'datasets' => $datasets];
    }

    private function graduatesByStageByYear(int $programId, Carbon $startDate, Carbon $endDate): array
    {
        $labels = $this->yearLabelsInRange($startDate, $endDate);

        if ($labels === []) {
            return ['labels' => [], 'datasets' => []];
        }

        // Single query with GROUP BY year instead of per-year loops
        [$rangeStart, $rangeEnd] = [Carbon::create((int) $labels[0], 1, 1)->startOfDay(), Carbon::create((int) $labels[count($labels) - 1], 12, 31)->endOfDay()];

        $rows = DB::table('education_enrollments')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'education_enrollments.beneficiary_id')
            ->where('memberships.program_id', $programId)
            ->whereBetween('education_enrollments.academic_year_start_date', [$rangeStart, $rangeEnd])
            ->where('education_enrollments.enrollment_status', 'completed')
            ->where(function ($query) {
                $query->where('education_enrollments.grade_level', '6')
                    ->orWhere('education_enrollments.grade_level', '10')
                    ->orWhere('education_enrollments.grade_level', '12');
            })
            ->selectRaw(
                'YEAR(education_enrollments.academic_year_start_date) as year,
                ' . $this->stageCaseSql('education_enrollments.grade_level') . ' as stage,
                COUNT(DISTINCT education_enrollments.beneficiary_id) as total'
            )
            ->groupByRaw(
                'YEAR(education_enrollments.academic_year_start_date),
                ' . $this->stageCaseSql('education_enrollments.grade_level')
            )
            ->orderBy('year')
            ->get()
            ->groupBy('year');

        $stages = ['elementary', 'high_school', 'shs', 'college'];
        $series = [];

        foreach ($stages as $stage) {
            $series[$stage] = array_fill(0, count($labels), 0);
        }

        foreach ($rows as $year => $stageRows) {
            $index = array_search((string) $year, $labels, true);
            if ($index === false) {
                continue;
            }
            foreach ($stageRows as $row) {
                $stage = $row->stage;
                if (isset($series[$stage])) {
                    $series[$stage][$index] = (int) $row->total;
                }
            }
        }

        // College graduates via single SQL subquery (latest completed enrollment per beneficiary)
        $collegeGrads = DB::table('education_enrollments as e')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'e.beneficiary_id')
            ->joinSub(
                DB::table('education_enrollments')
                    ->select('beneficiary_id', DB::raw('MAX(academic_year_start_date) as max_date'))
                    ->where('education_level', 'college')
                    ->groupBy('beneficiary_id'),
                'latest',
                fn ($join) => $join->on('e.beneficiary_id', '=', 'latest.beneficiary_id')
                    ->on('e.academic_year_start_date', '=', 'latest.max_date')
            )
            ->where('memberships.program_id', $programId)
            ->where('e.education_level', 'college')
            ->where('e.enrollment_status', 'completed')
            ->whereBetween('e.academic_year_start_date', [$rangeStart, $rangeEnd])
            ->select('e.beneficiary_id', DB::raw('YEAR(e.academic_year_start_date) as year'))
            ->distinct()
            ->get()
            ->groupBy('year');

        foreach ($collegeGrads as $year => $grads) {
            $index = array_search((string) $year, $labels, true);
            if ($index !== false) {
                $series['college'][$index] = count($grads);
            }
        }

        return [
            'labels' => $this->toAcademicYearLabels($labels),
            'datasets' => array_map(
                fn(string $stage) => [
                    'label' => Str::headline($stage),
                    'data' => $series[$stage],
                    'fill' => false,
                ],
                $stages
            ),
        ];
    }

    private function scholarCountsByEducationalStage(int $programId, Carbon $startDate, Carbon $endDate): array
    {
        // Subquery to get unique beneficiaries who are scholars in the program within the date range
        $scholarBeneficiaries = DB::table('beneficiaries')
            ->join('beneficiary_statuses as statuses', 'statuses.beneficiary_id', '=', 'beneficiaries.id')
            ->join('beneficiary_status_types as types', 'types.id', '=', 'statuses.beneficiary_status_type_id')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'beneficiaries.id')
            ->where('memberships.program_id', $programId)
            ->whereRaw('LOWER(types.status_name) LIKE ?', ['%' . Str::lower('scholar') . '%'])
            ->where('statuses.start_date', '<=', $endDate->toDateString())
            ->where(function ($query) use ($endDate) {
                $query->whereNull('statuses.end_date')
                    ->orWhere('statuses.end_date', '>=', $endDate->toDateString());
            })
            ->distinct()
            ->pluck('beneficiaries.id');

        // Get the most recent education enrollment for each scholar beneficiary within the date range
        $rows = DB::table('education_enrollments as e1')
            ->whereIn('e1.beneficiary_id', $scholarBeneficiaries)
            ->joinSub($this->latestEnrollmentSubQuery($startDate, $endDate), 'latest', function ($join) {
                $join->on('e1.beneficiary_id', '=', 'latest.beneficiary_id')
                     ->on('e1.id', '=', 'latest.max_id');
            })
            ->selectRaw(
                $this->stageCaseSql('e1.grade_level') . " as stage,
                COUNT(DISTINCT e1.beneficiary_id) as total"
            )
            ->groupByRaw($this->stageCaseSql('e1.grade_level'))
            ->get();

        $counts = $this->fillStageSeries($rows->pluck('total', 'stage')->all());

        return [
            'labels' => ['Elementary', 'High School', 'SHS', 'College'],
            'data' => array_values($counts),
        ];
    }

    private function scholarCountsByEducationalStageByYear(int $programId, Carbon $startDate, Carbon $endDate): array
    {
        $labels = $this->yearLabelsInRange($startDate, $endDate);

        if ($labels === []) {
            return ['labels' => [], 'datasets' => []];
        }

        // Pre-fetch scholar beneficiaries once for the full range
        $firstYear = (int) $labels[0];
        $lastYear = (int) $labels[count($labels) - 1];
        $yearStart = Carbon::create($firstYear, 1, 1)->startOfDay();
        $yearEnd = Carbon::create($lastYear, 12, 31)->endOfDay();

        $allScholarBeneficiaries = DB::table('beneficiaries')
            ->join('beneficiary_statuses as statuses', 'statuses.beneficiary_id', '=', 'beneficiaries.id')
            ->join('beneficiary_status_types as types', 'types.id', '=', 'statuses.beneficiary_status_type_id')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'beneficiaries.id')
            ->where('memberships.program_id', $programId)
            ->whereRaw('LOWER(types.status_name) LIKE ?', ['%' . Str::lower('scholar') . '%'])
            ->where('statuses.start_date', '<=', $yearEnd->toDateString())
            ->where(function ($query) use ($yearEnd) {
                $query->whereNull('statuses.end_date')
                    ->orWhere('statuses.end_date', '>=', $yearEnd->toDateString());
            })
            ->distinct()
            ->pluck('beneficiaries.id');

        $stages = ['elementary', 'high_school', 'shs', 'college'];
        $series = [];

        foreach ($stages as $stage) {
            $series[$stage] = array_fill(0, count($labels), 0);
        }

        $rows = DB::table('education_enrollments as e1')
            ->whereIn('e1.beneficiary_id', $allScholarBeneficiaries)
            ->joinSub(
                DB::table('education_enrollments')
                    ->select('beneficiary_id', DB::raw('YEAR(academic_year_start_date) as yr'), DB::raw('MAX(id) as max_id'))
                    ->where('academic_year_start_date', '<=', $yearEnd->toDateString())
                    ->where('academic_year_end_date', '>=', $yearStart->toDateString())
                    ->groupByRaw('beneficiary_id, YEAR(academic_year_start_date)'),
                'latest',
                function ($join) {
                    $join->on('e1.beneficiary_id', '=', 'latest.beneficiary_id')
                         ->on('e1.id', '=', 'latest.max_id');
                }
            )
            ->selectRaw(
                $this->stageCaseSql('e1.grade_level') . ' as stage,
                latest.yr as year,
                COUNT(DISTINCT e1.beneficiary_id) as total'
            )
            ->groupByRaw($this->stageCaseSql('e1.grade_level') . ', latest.yr')
            ->get();

        foreach ($rows as $row) {
            $index = array_search((string) $row->year, $labels, true);
            if ($index !== false && isset($series[$row->stage])) {
                $series[$row->stage][$index] = (int) $row->total;
            }
        }

        return [
            'labels' => $this->toAcademicYearLabels($labels),
            'datasets' => array_map(
                fn(string $stage) => [
                    'label' => Str::headline($stage),
                    'data' => $series[$stage],
                    'fill' => false,
                ],
                $stages
            ),
        ];
    }

    private function scholarCountsByEducationalStageForRange(int $programId, Carbon $startDate, Carbon $endDate): array
    {
        $scholarBeneficiaries = DB::table('beneficiaries')
            ->join('beneficiary_statuses as statuses', 'statuses.beneficiary_id', '=', 'beneficiaries.id')
            ->join('beneficiary_status_types as types', 'types.id', '=', 'statuses.beneficiary_status_type_id')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'beneficiaries.id')
            ->where('memberships.program_id', $programId)
            ->whereRaw('LOWER(types.status_name) LIKE ?', ['%' . Str::lower('scholar') . '%'])
            ->where('statuses.start_date', '<=', $endDate->toDateString())
            ->where(function ($query) use ($endDate) {
                $query->whereNull('statuses.end_date')
                    ->orWhere('statuses.end_date', '>=', $endDate->toDateString());
            })
            ->distinct()
            ->pluck('beneficiaries.id');

        $rows = DB::table('education_enrollments as e1')
            ->whereIn('e1.beneficiary_id', $scholarBeneficiaries)
            ->joinSub($this->latestEnrollmentSubQuery($startDate, $endDate), 'latest', function ($join) {
                $join->on('e1.beneficiary_id', '=', 'latest.beneficiary_id')
                     ->on('e1.id', '=', 'latest.max_id');
            })
            ->selectRaw(
                $this->stageCaseSql('e1.grade_level') . " as stage,
                COUNT(DISTINCT e1.beneficiary_id) as total"
            )
            ->groupByRaw($this->stageCaseSql('e1.grade_level'))
            ->get();

        return $this->fillStageSeries($rows->pluck('total', 'stage')->all());
    }

    private function newPlayerCountsBySportsType(int $programId, Carbon $startDate, Carbon $endDate): array
    {
        [$rangeStart, $rangeEnd] = $this->dateRangeBounds($startDate, $endDate);

        $rows = DB::table('activity_participants')
            ->join('activities', 'activities.id', '=', 'activity_participants.activity_id')
            ->join('sport_types', 'sport_types.id', '=', 'activities.sport_type_id')
            ->join('beneficiaries', 'beneficiaries.id', '=', 'activity_participants.beneficiary_id')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'beneficiaries.id')
            ->where('memberships.program_id', $programId)
            ->whereNotNull('activity_participants.beneficiary_id')
            ->where(function ($query) use ($rangeStart, $rangeEnd) {
                $query->whereBetween('activity_participants.joined_at', [$rangeStart, $rangeEnd])
                    ->orWhere(function ($nested) use ($rangeStart, $rangeEnd) {
                        $nested->whereNull('activity_participants.joined_at')
                            ->whereBetween('activity_participants.created_at', [$rangeStart, $rangeEnd]);
                    });
            })
            ->selectRaw('sport_types.name as sport_type, COUNT(DISTINCT activity_participants.beneficiary_id) as total')
            ->groupBy('sport_types.name')
            ->orderBy('sport_types.name')
            ->get();

        return [
            'labels' => $rows->pluck('sport_type')->all(),
            'data' => $rows->pluck('total')->map(fn($value) => (int) $value)->all(),
        ];
    }

    private function newPlayerCountsBySportsTypeByYear(int $programId, Carbon $startDate, Carbon $endDate): array
    {
        $labels = $this->yearLabelsInRange($startDate, $endDate);

        if ($labels === []) {
            return ['labels' => [], 'datasets' => []];
        }

        // Single query with GROUP BY year and sport_type instead of per-year loops
        [$rangeStart, $rangeEnd] = $this->dateRangeBounds($startDate, $endDate);

        $rows = DB::table('activity_participants')
            ->join('activities', 'activities.id', '=', 'activity_participants.activity_id')
            ->join('sport_types', 'sport_types.id', '=', 'activities.sport_type_id')
            ->join('beneficiaries', 'beneficiaries.id', '=', 'activity_participants.beneficiary_id')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'beneficiaries.id')
            ->where('memberships.program_id', $programId)
            ->whereNotNull('activity_participants.beneficiary_id')
            ->where(function ($query) use ($rangeStart, $rangeEnd) {
                $query->whereBetween('activity_participants.joined_at', [$rangeStart, $rangeEnd])
                    ->orWhere(function ($nested) use ($rangeStart, $rangeEnd) {
                        $nested->whereNull('activity_participants.joined_at')
                            ->whereBetween('activity_participants.created_at', [$rangeStart, $rangeEnd]);
                    });
            })
            ->selectRaw(
                'YEAR(COALESCE(activity_participants.joined_at, activity_participants.created_at)) as year,
                sport_types.name as sport_type,
                COUNT(DISTINCT activity_participants.beneficiary_id) as total'
            )
            ->groupByRaw(
                'YEAR(COALESCE(activity_participants.joined_at, activity_participants.created_at)),
                sport_types.name'
            )
            ->orderBy('sport_types.name')
            ->orderBy('year')
            ->get()
            ->groupBy('year');

        $sportTypes = $rows->flatMap(function ($yearRows) {
            return $yearRows->pluck('sport_type');
        })->unique()->values()->all();

        $datasets = [];

        foreach ($sportTypes as $sportType) {
            $series = array_fill(0, count($labels), 0);

            foreach ($labels as $index => $yearLabel) {
                $yearRows = $rows->get((int) $yearLabel, collect());
                $row = $yearRows->firstWhere('sport_type', $sportType);
                if ($row) {
                    $series[$index] = (int) $row->total;
                }
            }

            $datasets[] = [
                'label' => $sportType,
                'data' => $series,
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    private function activePlayersBySportsType(int $programId, Carbon $startDate, Carbon $endDate): array
    {
        $rows = DB::table('activity_participants')
            ->join('activities', 'activities.id', '=', 'activity_participants.activity_id')
            ->join('sport_types', 'sport_types.id', '=', 'activities.sport_type_id')
            ->join('beneficiaries', 'beneficiaries.id', '=', 'activity_participants.beneficiary_id')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'beneficiaries.id')
            ->where('memberships.program_id', $programId)
            ->whereNotNull('activity_participants.beneficiary_id')
            ->where(function ($q) use ($endDate) {
                $q->whereNull('activity_participants.exited_at')
                    ->orWhere('activity_participants.exited_at', '>', $endDate->copy()->endOfDay());
            })
            ->selectRaw('sport_types.name as sport_type, COUNT(DISTINCT activity_participants.beneficiary_id) as total')
            ->groupBy('sport_types.name')
            ->orderBy('sport_types.name')
            ->get();

        return [
            'labels' => $rows->pluck('sport_type')->all(),
            'data' => $rows->pluck('total')->map(fn($value) => (int) $value)->all(),
        ];
    }

    private function activePlayersBySportsTypeByYear(int $programId, Carbon $startDate, Carbon $endDate): array
    {
        $labels = $this->yearLabelsInRange($startDate, $endDate);

        if ($labels === []) {
            return ['labels' => [], 'datasets' => []];
        }

        $participants = DB::table('activity_participants')
            ->join('activities', 'activities.id', '=', 'activity_participants.activity_id')
            ->join('sport_types', 'sport_types.id', '=', 'activities.sport_type_id')
            ->join('beneficiaries', 'beneficiaries.id', '=', 'activity_participants.beneficiary_id')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'beneficiaries.id')
            ->where('memberships.program_id', $programId)
            ->whereNotNull('activity_participants.beneficiary_id')
            ->where('activity_participants.joined_at', '<=', $endDate)
            ->where(function ($q) use ($startDate) {
                $q->whereNull('activity_participants.exited_at')
                  ->orWhere('activity_participants.exited_at', '>=', $startDate);
            })
            ->select(
                'activity_participants.beneficiary_id',
                'activity_participants.joined_at',
                'activity_participants.exited_at',
                'sport_types.name as sport_type'
            )
            ->distinct()
            ->get();

        $sportTypes = $participants->pluck('sport_type')->unique()->values()->all();
        $datasets = [];

        foreach ($sportTypes as $sportType) {
            $sportParticipants = $participants->where('sport_type', $sportType);
            $series = [];

            foreach ($labels as $yearLabel) {
                $yearEnd = Carbon::create((int) $yearLabel, 12, 31)->endOfDay();
                $active = $sportParticipants->filter(function ($p) use ($yearEnd) {
                    $joinedBefore = Carbon::parse($p->joined_at)->lessThanOrEqualTo($yearEnd);
                    $notExited = $p->exited_at === null || Carbon::parse($p->exited_at)->greaterThan($yearEnd);
                    return $joinedBefore && $notExited;
                })->count();
                $series[] = $active;
            }

            $datasets[] = [
                'label' => $sportType,
                'data' => $series,
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    private function trainingTimeSeriesMerged(?int $programId, Carbon $startDate, Carbon $endDate, string $period, ?int $sportTypeId = null, ?string $ageGroup = null): array
    {
        if (!$programId) {
            return [
                'trainingSessionAttendance' => ['labels' => [], 'data' => []],
                'uniqueVisits' => ['labels' => [], 'data' => []],
            ];
        }

        $buckets = $this->generateBuckets($startDate, $endDate, $period);
        $attendanceData = array_fill(0, count($buckets['labels']), 0);
        $visitsData = array_fill(0, count($buckets['labels']), 0);
        [$rangeStart, $rangeEnd] = $this->dateRangeBounds($startDate, $endDate);

        $results = DB::table('attendances')
            ->join('activity_sessions', 'activity_sessions.id', '=', 'attendances.activity_session_id')
            ->join('activities', 'activities.id', '=', 'activity_sessions.activity_id')
            ->join('activity_types', 'activity_types.id', '=', 'activities.activity_type_id')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'attendances.beneficiary_id')
            ->where('memberships.program_id', $programId)
            ->where('activity_types.name', 'Training Session')
            ->whereBetween('activity_sessions.schedule', [$rangeStart, $rangeEnd])
            ->when($sportTypeId, fn($q) => $q->where('activities.sport_type_id', $sportTypeId))
            ->when($ageGroup, fn($q) => $this->applyAgeGroupFilter($q, $ageGroup))
            ->groupBy(DB::raw('DATE(activity_sessions.schedule)'))
            ->selectRaw(
                "DATE(activity_sessions.schedule) as date_col,
                COUNT(*) as total,
                SUM(CASE WHEN attendances.attendance_status = 'present' THEN 1 ELSE 0 END) as present,
                COUNT(DISTINCT CASE WHEN attendances.attendance_status = 'present' THEN CONCAT(attendances.beneficiary_id, ':', DATE(activity_sessions.schedule)) END) as visits"
            )
            ->get()
            ->keyBy('date_col');

        foreach ($buckets['ranges'] as $index => $range) {
            $presentSum = 0;
            $totalSum = 0;
            $visitsSum = 0;
            $cursor = $range['start']->copy();

            while ($cursor->lte($range['end'])) {
                $dateKey = $cursor->toDateString();
                $dayResult = $results->get($dateKey);
                if ($dayResult) {
                    $presentSum += (int) $dayResult->present;
                    $totalSum += (int) $dayResult->total;
                    $visitsSum += (int) $dayResult->visits;
                }
                $cursor->addDay();
            }

            $attendanceData[$index] = $totalSum === 0 ? 0 : round(($presentSum / $totalSum) * 100, 2);
            $visitsData[$index] = $visitsSum;
        }

        return [
            'trainingSessionAttendance' => ['labels' => $buckets['labels'], 'data' => $attendanceData],
            'uniqueVisits' => ['labels' => $buckets['labels'], 'data' => $visitsData],
            'average' => count($attendanceData) > 0 ? round(array_sum($attendanceData) / count($attendanceData), 2) : 0.0,
        ];
    }

    private function trainingTimeSeriesByYearMerged(?int $programId, Carbon $startDate, Carbon $endDate, ?int $sportTypeId = null, ?string $ageGroup = null): array
    {
        if (!$programId) {
            return [
                'trainingSessionAttendance' => ['labels' => [], 'data' => []],
                'uniqueVisitsByYear' => ['labels' => [], 'data' => []],
            ];
        }

        $labels = $this->yearLabelsInRange($startDate, $endDate);
        $attendanceData = array_fill(0, count($labels), 0);
        $visitsData = array_fill(0, count($labels), 0);

        if ($labels === []) {
            return [
                'trainingSessionAttendance' => ['labels' => [], 'data' => $attendanceData],
                'uniqueVisitsByYear' => ['labels' => [], 'data' => $visitsData],
            ];
        }

        [$rangeStart, $rangeEnd] = $this->dateRangeBounds($startDate, $endDate);

        $results = DB::table('attendances')
            ->join('activity_sessions', 'activity_sessions.id', '=', 'attendances.activity_session_id')
            ->join('activities', 'activities.id', '=', 'activity_sessions.activity_id')
            ->join('activity_types', 'activity_types.id', '=', 'activities.activity_type_id')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'attendances.beneficiary_id')
            ->where('memberships.program_id', $programId)
            ->where('activity_types.name', 'Training Session')
            ->whereBetween('activity_sessions.schedule', [$rangeStart, $rangeEnd])
            ->when($sportTypeId, fn($q) => $q->where('activities.sport_type_id', $sportTypeId))
            ->when($ageGroup, fn($q) => $this->applyAgeGroupFilter($q, $ageGroup))
            ->groupBy(DB::raw('YEAR(activity_sessions.schedule)'))
            ->selectRaw(
                'YEAR(activity_sessions.schedule) as year,
                COUNT(*) as total,
                SUM(CASE WHEN attendances.attendance_status = ? THEN 1 ELSE 0 END) as present,
                COUNT(DISTINCT CASE WHEN attendances.attendance_status = ? THEN CONCAT(attendances.beneficiary_id, \':\', DATE(activity_sessions.schedule)) END) as visits',
                ['present', 'present']
            )
            ->get()
            ->keyBy('year');

        foreach ($labels as $index => $yearLabel) {
            $row = $results->get((int) $yearLabel);
            if ($row) {
                $total = (int) $row->total;
                $present = (int) $row->present;
                $attendanceData[$index] = $total === 0 ? 0 : round(($present / $total) * 100, 2);
                $visitsData[$index] = (int) $row->visits;
            }
        }

        return [
            'trainingSessionAttendance' => ['labels' => $labels, 'data' => $attendanceData],
            'uniqueVisitsByYear' => ['labels' => $labels, 'data' => $visitsData],
        ];
    }

    private function buildDonorMetrics(array $filters, Staff $staff, ?Program $programScope): array
    {
        $startDate = Carbon::parse($filters['startDate']);
        $endDate = Carbon::parse($filters['endDate']);
        $effectiveProgram = $this->canUseProgramFilter($staff)
            ? $programScope
            : $staff->staffProgram();

        // Compute financial metrics once and reuse for percentages
        $fundsReceived = $this->sumDonations($effectiveProgram, $startDate, $endDate);
        $fundsAllocated = $this->sumAllocations($effectiveProgram, $startDate, $endDate);

        $yearsInRange = $this->yearLabelsInRange($startDate, $endDate);
        $fundingTarget = $this->sumFundingTargetsForYears($effectiveProgram, array_map('intval', $yearsInRange));

        $bucketInterval = $this->determineBucketInterval($startDate, $endDate, $filters['period']);
        $receivedData = $this->fundsReceivedByPeriod($effectiveProgram, $startDate, $endDate, $bucketInterval);
        $allocatedData = $this->fundsAllocatedByPeriod($effectiveProgram, $startDate, $endDate, $bucketInterval);

        return [
            'newDonors' => DB::table('donors')
                ->when($effectiveProgram, fn($query) => $query->whereExists(function ($subquery) use ($effectiveProgram) {
                    $subquery->selectRaw('1')
                        ->from('donations')
                        ->whereColumn('donations.donor_id', 'donors.id')
                        ->where('donations.program_id', $effectiveProgram->id);
                }))
                ->whereBetween('donors.created_at', [$startDate, $endDate])
                ->count(),
            'grantsReceived' => $this->countGrants($effectiveProgram, $startDate, $endDate),
            'fundsReceived' => $fundsReceived,
            'fundsAllocated' => $fundsAllocated,
            'fundingTarget' => $fundingTarget,
            'receivedProgressPercent' => $fundingTarget > 0 ? min(100, round(($fundsReceived / $fundingTarget) * 100, 1)) : 0,
            'allocatedVsReceivedPercent' => $fundsReceived > 0 ? min(100, round(($fundsAllocated / $fundsReceived) * 100, 1)) : 0,
            'upcomingDeliverables' => $this->upcomingDeliverablesCount($effectiveProgram, $endDate),
            'deliverablesGanttTasks' => $this->upcomingDeliverablesTasks($effectiveProgram, $endDate),
            'trendPeriod' => $bucketInterval,
            'trendLabels' => $receivedData['labels'],
            'trendReceived' => $receivedData['values'],
            'trendAllocated' => $allocatedData['values'],
        ];
    }

    private function buildFilterOptions(): array
    {
        return [
            'gradeLevels' => ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'],
            'ageGroups' => [
                ['value' => 'under_12', 'label' => 'Under 12'],
                ['value' => '12_15', 'label' => '12 to 15'],
                ['value' => '16_18', 'label' => '16 to 18'],
                ['value' => '19_plus', 'label' => '19 and above'],
            ],
            'sportTypes' => DB::table('sport_types')
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn($sportType) => [
                    'id' => $sportType->id,
                    'name' => $sportType->name,
                ])
                ->all(),
        ];
    }

    private function educationProgramId(): ?int
    {
        if ($this->cachedEducationProgramId === null) {
            $this->cachedEducationProgramId = $this->programIdByName('education');
        }
        return $this->cachedEducationProgramId;
    }

    private function sportsProgramId(): ?int
    {
        if ($this->cachedSportsProgramId === null) {
            $this->cachedSportsProgramId = $this->programIdByName('sports');
        }
        return $this->cachedSportsProgramId;
    }

    private function graduatesByStage(int $programId, int $academicYear): array
    {
        return $this->graduatesByStageForAcademicYear($programId, $academicYear);
    }

    private function graduatesByStageForAcademicYear(int $programId, int $academicYear): array
    {
        $graduates = [
            'elementary' => 0,
            'high_school' => 0,
            'shs' => 0,
            'college' => 0,
        ];

        $rows = DB::table('education_enrollments')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'education_enrollments.beneficiary_id')
            ->where('memberships.program_id', $programId)
            ->whereBetween('education_enrollments.academic_year_start_date', [Carbon::create($academicYear, 1, 1)->startOfDay(), Carbon::create($academicYear, 12, 31)->endOfDay()])
            ->where('education_enrollments.enrollment_status', 'completed')
            ->where(function ($query) {
                $query->where('education_enrollments.grade_level', '6')
                    ->orWhere('education_enrollments.grade_level', '10')
                    ->orWhere('education_enrollments.grade_level', '12');
            })
            ->select('education_enrollments.education_level', DB::raw('COUNT(education_enrollments.beneficiary_id) as total'))
            ->groupBy('education_enrollments.education_level')
            ->get();

        foreach ($rows as $row) {
            $graduates[$row->education_level] = (int) $row->total;
        }

        $collegeCount = DB::table('education_enrollments as e')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'e.beneficiary_id')
            ->joinSub(
                DB::table('education_enrollments')
                    ->select('beneficiary_id', DB::raw('MAX(academic_year_start_date) as max_date'))
                    ->where('education_level', 'college')
                    ->groupBy('beneficiary_id'),
                'latest',
                fn ($join) => $join->on('e.beneficiary_id', '=', 'latest.beneficiary_id')
                    ->on('e.academic_year_start_date', '=', 'latest.max_date')
            )
            ->where('memberships.program_id', $programId)
            ->where('e.education_level', 'college')
            ->where('e.enrollment_status', 'completed')
            ->whereYear('e.academic_year_start_date', $academicYear)
            ->count();

        $graduates['college'] = (int) $collegeCount;

        return $this->fillStageSeries($graduates);
    }

    private function graduatesByStageAggregate(int $programId, Carbon $startDate, Carbon $endDate): array
    {
        $graduates = [
            'elementary' => 0,
            'high_school' => 0,
            'shs' => 0,
            'college' => 0,
        ];

        $rows = DB::table('education_enrollments')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'education_enrollments.beneficiary_id')
            ->where('memberships.program_id', $programId)
            ->whereBetween('education_enrollments.academic_year_start_date', [$startDate, $endDate])
            ->where('education_enrollments.enrollment_status', 'completed')
            ->where(function ($query) {
                $query->where('education_enrollments.grade_level', '6')
                    ->orWhere('education_enrollments.grade_level', '10')
                    ->orWhere('education_enrollments.grade_level', '12');
            })
            ->select('education_enrollments.education_level', DB::raw('COUNT(DISTINCT education_enrollments.beneficiary_id) as total'))
            ->groupBy('education_enrollments.education_level')
            ->get();

        foreach ($rows as $row) {
            $stage = $this->normalizeEducationLevelStage($row->education_level);
            if ($stage && isset($graduates[$stage])) {
                $graduates[$stage] = (int) $row->total;
            }
        }

        $collegeCount = DB::table('education_enrollments as e')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'e.beneficiary_id')
            ->joinSub(
                DB::table('education_enrollments')
                    ->select('beneficiary_id', DB::raw('MAX(academic_year_start_date) as max_date'))
                    ->where('education_level', 'college')
                    ->groupBy('beneficiary_id'),
                'latest',
                fn ($join) => $join->on('e.beneficiary_id', '=', 'latest.beneficiary_id')
                    ->on('e.academic_year_start_date', '=', 'latest.max_date')
            )
            ->where('memberships.program_id', $programId)
            ->where('e.education_level', 'college')
            ->where('e.enrollment_status', 'completed')
            ->whereBetween('e.academic_year_start_date', [$startDate, $endDate])
            ->count();

        $graduates['college'] = (int) $collegeCount;

        return $this->fillStageSeries($graduates);
    }

    private function averageGradesByStage(int $programId, Carbon $startDate, Carbon $endDate): array
    {
        $rows = DB::table('academic_records')
            ->join('education_enrollments', 'education_enrollments.id', '=', 'academic_records.education_enrollment_id')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'academic_records.beneficiary_id')
            ->where('memberships.program_id', $programId)
            ->where('education_enrollments.academic_year_start_date', '<=', $endDate->toDateString())
            ->where('education_enrollments.academic_year_end_date', '>=', $startDate->toDateString())
            ->selectRaw(
                $this->stageCaseSql('education_enrollments.grade_level') . " as stage,
                ROUND(AVG(academic_records.gwa), 2) as average"
            )
            ->groupBy('stage')
            ->pluck('average', 'stage');

        return $this->fillStageSeries($rows->all());
    }

    private function programEducationEnrollments(int $programId): array
    {
        if (!isset($this->cachedProgramEnrollments[$programId])) {
            $this->cachedProgramEnrollments[$programId] = DB::table('education_enrollments')
                ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'education_enrollments.beneficiary_id')
                ->where('memberships.program_id', $programId)
                ->select(
                    'education_enrollments.id',
                    'education_enrollments.beneficiary_id',
                    'education_enrollments.education_level',
                    'education_enrollments.grade_level',
                    'education_enrollments.enrollment_status',
                    'education_enrollments.academic_year_start_date',
                    'education_enrollments.academic_year_end_date'
                )
                ->orderBy('education_enrollments.beneficiary_id')
                ->orderBy('education_enrollments.academic_year_start_date')
                ->orderBy('education_enrollments.academic_year_end_date')
                ->orderBy('education_enrollments.id')
                ->get()
                ->groupBy('beneficiary_id')
                ->all();
        }

        return $this->cachedProgramEnrollments[$programId];
    }

    private function enrollmentIntersectsAcademicYear(object $enrollment, int $academicYear): bool
    {
        return Carbon::parse($enrollment->academic_year_start_date)->year === $academicYear;
    }

    private function normalizeEducationLevelStage(?string $educationLevel): ?string
    {
        return match (Str::lower(trim((string) $educationLevel))) {
            'elementary' => 'elementary',
            'high_school' => 'high_school',
            'shs' => 'shs',
            'college' => 'college',
            default => null,
        };
    }

    private function terminalGradeForStage(string $stage): ?string
    {
        return match ($stage) {
            'elementary' => '6',
            'high_school' => '10',
            'shs' => '12',
            default => null,
        };
    }

    private function isTerminalStageEnrollment(object $enrollment): bool
    {
        $stage = $this->stageFromGradeLevel((string) $enrollment->grade_level);
        $terminalGrade = $stage ? $this->terminalGradeForStage($stage) : null;

        return $stage !== null && $terminalGrade !== null && (string) $enrollment->grade_level === $terminalGrade;
    }

    private function stageTransitionRateBreakdown(int $programId, int $academicYear): array
    {
        $transitions = $this->educationBreakdownsByYears($programId, [$academicYear])['transition'][$academicYear] ?? [];
        $rates = [];
        $successCounts = [];
        $totalCounts = [];

        foreach ($transitions as $stage => $values) {
            if ($values['total'] === 0) {
                continue;
            }
            $rates[$stage] = round(($values['success'] / $values['total']) * 100, 2);
            $successCounts[$stage] = $values['success'];
            $totalCounts[$stage] = $values['total'];
        }

        return [
            'rates' => $rates,
            'successCounts' => $successCounts,
            'totalCounts' => $totalCounts,
            'academicYear' => $academicYear,
        ];
    }

    private function stageCompletionRateBreakdown(int $programId, int $academicYear): array
    {
        $rates = $this->educationBreakdownsByYears($programId, [$academicYear])['completion'][$academicYear] ?? [];

        $rateValues = [];
        $completedCounts = [];
        $totalCounts = [];

        foreach ($rates as $stage => $values) {
            if ($values['total'] === 0) {
                continue;
            }
            $rateValues[$stage] = round(($values['completed'] / $values['total']) * 100, 2);
            $completedCounts[$stage] = $values['completed'];
            $totalCounts[$stage] = $values['total'];
        }

        return [
            'rates' => $rateValues,
            'completedCounts' => $completedCounts,
            'totalCounts' => $totalCounts,
            'academicYear' => $academicYear,
        ];
    }

    private function yearLabelsInRange(Carbon $startDate, Carbon $endDate): array
    {
        $years = [];

        for ($year = $startDate->year; $year <= $endDate->year; $year++) {
            $years[] = (string) $year;
        }

        return $years;
    }

    private function toAcademicYearLabels(array $labels): array
    {
        return array_map(fn(string $label) => $label . '-' . ((int) $label + 1), $labels);
    }

    private function transitionRatesByAcademicYear(int $programId, Carbon $startDate, Carbon $endDate): array
    {
        $labels = $this->yearLabelsInRange($startDate, $endDate);

        if ($labels === []) {
            return ['labels' => [], 'datasets' => []];
        }

        $series = [
            'elementary' => array_fill(0, count($labels), 0),
            'high_school' => array_fill(0, count($labels), 0),
            'shs' => array_fill(0, count($labels), 0),
        ];
        $successCounts = [
            'elementary' => array_fill(0, count($labels), 0),
            'high_school' => array_fill(0, count($labels), 0),
            'shs' => array_fill(0, count($labels), 0),
        ];
        $totalCounts = [
            'elementary' => array_fill(0, count($labels), 0),
            'high_school' => array_fill(0, count($labels), 0),
            'shs' => array_fill(0, count($labels), 0),
        ];

        $breakdowns = $this->educationBreakdownsByYears($programId, array_map('intval', $labels));

        foreach ($labels as $index => $academicYearLabel) {
            $academicYear = (int) $academicYearLabel;
            $breakdown = $breakdowns['transition'][$academicYear] ?? [];

            foreach ($series as $stage => $values) {
                $stageValues = $breakdown[$stage] ?? ['total' => 0, 'success' => 0];
                $total = (int) ($stageValues['total'] ?? 0);
                $success = (int) ($stageValues['success'] ?? 0);

                $series[$stage][$index] = $total > 0 ? round(($success / $total) * 100, 2) : 0;
                $successCounts[$stage][$index] = $success;
                $totalCounts[$stage][$index] = $total;
            }
        }

        return [
            'labels' => $this->toAcademicYearLabels($labels),
            'datasets' => [
                [
                    'label' => 'Elementary to High School',
                    'data' => $series['elementary'],
                    'successCounts' => $successCounts['elementary'],
                    'totalCounts' => $totalCounts['elementary'],
                ],
                [
                    'label' => 'High School to SHS',
                    'data' => $series['high_school'],
                    'successCounts' => $successCounts['high_school'],
                    'totalCounts' => $totalCounts['high_school'],
                ],
                [
                    'label' => 'SHS to College',
                    'data' => $series['shs'],
                    'successCounts' => $successCounts['shs'],
                    'totalCounts' => $totalCounts['shs'],
                ],
            ],
        ];
    }

    private function completionRatesByAcademicYear(int $programId, Carbon $startDate, Carbon $endDate): array
    {
        $labels = $this->yearLabelsInRange($startDate, $endDate);

        if ($labels === []) {
            return ['labels' => [], 'datasets' => []];
        }

        $series = [
            'elementary' => array_fill(0, count($labels), 0),
            'high_school' => array_fill(0, count($labels), 0),
            'shs' => array_fill(0, count($labels), 0),
            'college' => array_fill(0, count($labels), 0),
        ];
        $completedCounts = [
            'elementary' => array_fill(0, count($labels), 0),
            'high_school' => array_fill(0, count($labels), 0),
            'shs' => array_fill(0, count($labels), 0),
            'college' => array_fill(0, count($labels), 0),
        ];
        $totalCounts = [
            'elementary' => array_fill(0, count($labels), 0),
            'high_school' => array_fill(0, count($labels), 0),
            'shs' => array_fill(0, count($labels), 0),
            'college' => array_fill(0, count($labels), 0),
        ];

        $breakdowns = $this->educationBreakdownsByYears($programId, array_map('intval', $labels));

        foreach ($labels as $index => $academicYearLabel) {
            $academicYear = (int) $academicYearLabel;
            $breakdown = $breakdowns['completion'][$academicYear] ?? [];

            foreach ($series as $stage => $values) {
                $stageValues = $breakdown[$stage] ?? ['total' => 0, 'completed' => 0];
                $total = (int) ($stageValues['total'] ?? 0);
                $completed = (int) ($stageValues['completed'] ?? 0);

                $series[$stage][$index] = $total > 0 ? round(($completed / $total) * 100, 2) : 0;
                $completedCounts[$stage][$index] = $completed;
                $totalCounts[$stage][$index] = $total;
            }
        }

        return [
            'labels' => $this->toAcademicYearLabels($labels),
            'datasets' => [
                [
                    'label' => 'Elementary',
                    'data' => $series['elementary'],
                    'completedCounts' => $completedCounts['elementary'],
                    'totalCounts' => $totalCounts['elementary'],
                ],
                [
                    'label' => 'High School',
                    'data' => $series['high_school'],
                    'completedCounts' => $completedCounts['high_school'],
                    'totalCounts' => $totalCounts['high_school'],
                ],
                [
                    'label' => 'SHS',
                    'data' => $series['shs'],
                    'completedCounts' => $completedCounts['shs'],
                    'totalCounts' => $totalCounts['shs'],
                ],
                [
                    'label' => 'College',
                    'data' => $series['college'],
                    'completedCounts' => $completedCounts['college'],
                    'totalCounts' => $totalCounts['college'],
                ],
            ],
        ];
    }

    private function averageSocioEmotionalByStage(?int $programId, Carbon $startDate, Carbon $endDate, ?int $categoryId): array
    {
        if (!$programId || !$categoryId) {
            return [];
        }

        $rows = DB::table('ffa_assessment_records')
            ->join('education_enrollments', 'education_enrollments.beneficiary_id', '=', 'ffa_assessment_records.beneficiary_id')
            ->join('assessment_categories', 'assessment_categories.id', '=', 'ffa_assessment_records.assessment_category_id')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'ffa_assessment_records.beneficiary_id')
            ->where('memberships.program_id', $programId)
            ->where('assessment_categories.id', $categoryId)
            ->where('education_enrollments.academic_year_start_date', '<=', $endDate->toDateString())
            ->where('education_enrollments.academic_year_end_date', '>=', $startDate->toDateString())
            ->whereBetween('ffa_assessment_records.date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw(
                $this->stageCaseSql('education_enrollments.grade_level') . " as stage,
                ROUND(AVG(ffa_assessment_records.score), 2) as average"
            )
            ->groupBy('stage')
            ->pluck('average', 'stage');

        return $this->fillStageSeries($rows->all());
    }

    private function socioEmotionalCategoryId(): ?int
    {
        if ($this->cachedSocioEmotionalCategoryId === null) {
            $this->cachedSocioEmotionalCategoryId = DB::table('assessment_categories')
                ->whereRaw('LOWER(assessment_name) = ?', ['socio-emotional'])
                ->value('id');
        }
        return $this->cachedSocioEmotionalCategoryId;
    }

    private function averageSocioEmotionalScore(?int $programId, Carbon $startDate, Carbon $endDate, ?int $categoryId): ?float
    {
        if (!$programId || !$categoryId) {
            return null;
        }

        $average = DB::table('ffa_assessment_records')
            ->join('beneficiaries', 'beneficiaries.id', '=', 'ffa_assessment_records.beneficiary_id')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'beneficiaries.id')
            ->where('memberships.program_id', $programId)
            ->where('ffa_assessment_records.assessment_category_id', $categoryId)
            ->whereBetween('ffa_assessment_records.date', [$startDate->toDateString(), $endDate->toDateString()])
            ->avg('ffa_assessment_records.score');

        return $average !== null ? round((float) $average, 2) : null;
    }

    private function countGrants(?Program $programScope, Carbon $startDate, Carbon $endDate): int
    {
        return (int) DB::table('grants')
            ->whereBetween('start_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->when($programScope, fn($q) => $q->whereExists(function ($sub) use ($programScope) {
                $sub->selectRaw('1')->from('grant_program')
                    ->whereColumn('grant_program.grant_id', 'grants.id')
                    ->where('grant_program.program_id', $programScope->id);
            }))
            ->count();
    }

    private function sumDonations(?Program $programScope, Carbon $startDate, Carbon $endDate): float
    {
        return (float) DB::table('donations')
            ->where('status', 'completed')
            ->when($programScope, fn($query) => $query->where('program_id', $programScope->id))
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');
    }

    private function sumAllocations(?Program $programScope, Carbon $startDate, Carbon $endDate): float
    {
        return (float) DB::table('allocations')
            ->when($programScope, function ($query) use ($programScope) {
                $query->whereIn('allocations.beneficiary_id', function ($sub) use ($programScope) {
                    $sub->select('beneficiary_id')
                        ->from('beneficiary_program_memberships')
                        ->where('program_id', $programScope->id);
                });
            })
            ->whereBetween('date_allocated', [$startDate->toDateString(), $endDate->toDateString()])
            ->sum(DB::raw('amount_cents / 100'));
    }

    private function sumFundingTargets(?Program $programScope, int $year): float
    {
        return $this->sumFundingTargetsForYears($programScope, [$year]);
    }

    private function sumFundingTargetsForYears(?Program $programScope, array $years): float
    {
        return (float) DB::table('funding_targets')
            ->when($programScope, fn($query) => $query->where('program_id', $programScope->id))
            ->whereIn('year', $years)
            ->sum(DB::raw('target_amount_cents / 100'));
    }

    private function competitionParticipationCount(array $filters, ?Program $programScope): int
    {
        $startDate = Carbon::parse($filters['startDate'])->startOfDay();
        $endDate = Carbon::parse($filters['endDate'])->endOfDay();

        return DB::table('competitions')
            ->join('competition_results', 'competition_results.competition_id', '=', 'competitions.id')
            ->when($programScope, fn($query) => $query->where('competitions.program_id', $programScope->id))
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('competition_results.date_given', [$startDate->toDateString(), $endDate->toDateString()])
                    ->orWhere(function ($nested) use ($startDate, $endDate) {
                        $nested->whereNull('competition_results.date_given')
                            ->whereBetween('competitions.start', [$startDate, $endDate]);
                    });
            })
            ->distinct('competitions.id')
            ->count('competitions.id');
    }

    private function upcomingDeliverablesCount(?Program $programScope, Carbon $endDate): int
    {
        return DB::table('deliverables')
            ->when($programScope, function ($query) use ($programScope) {
                $query->where(function ($nested) use ($programScope) {
                    $nested->whereExists(function ($subquery) use ($programScope) {
                        $subquery->selectRaw('1')
                            ->from('grants')
                            ->join('grant_program', 'grant_program.grant_id', '=', 'grants.id')
                            ->whereColumn('deliverables.grant_id', 'grants.id')
                            ->where('grant_program.program_id', $programScope->id);
                    })->orWhereExists(function ($subquery) use ($programScope) {
                        $subquery->selectRaw('1')
                            ->from('donations')
                            ->whereColumn('deliverables.donor_id', 'donations.donor_id')
                            ->where('donations.program_id', $programScope->id);
                    });
                });
            })
            ->whereNull('completed_at')
            ->whereBetween('end_date', [now()->toDateString(), $endDate->copy()->addDays(30)->toDateString()])
            ->count();
    }

    private function upcomingDeliverablesTasks(?Program $programScope, Carbon $endDate): array
    {
        return DB::table('deliverables')
            ->leftJoin('grants', 'grants.id', '=', 'deliverables.grant_id')
            ->when($programScope, function ($query) use ($programScope) {
                $query->where(function ($nested) use ($programScope) {
                    $nested->whereExists(function ($subquery) use ($programScope) {
                        $subquery->selectRaw('1')
                            ->from('grants')
                            ->join('grant_program', 'grant_program.grant_id', '=', 'grants.id')
                            ->whereColumn('deliverables.grant_id', 'grants.id')
                            ->where('grant_program.program_id', $programScope->id);
                    })->orWhereExists(function ($subquery) use ($programScope) {
                        $subquery->selectRaw('1')
                            ->from('donations')
                            ->whereColumn('deliverables.donor_id', 'donations.donor_id')
                            ->where('donations.program_id', $programScope->id);
                    });
                });
            })
            ->whereNull('deliverables.completed_at')
            ->whereBetween('deliverables.end_date', [now()->toDateString(), $endDate->copy()->addDays(30)->toDateString()])
            ->orderBy('deliverables.end_date')
            ->limit(15)
            ->get([
                'deliverables.id', 'deliverables.title', 'deliverables.description',
                'deliverables.start_date', 'deliverables.end_date', 'deliverables.progress',
                'deliverables.completed_at', 'grants.grant_name as grant_name',
            ])
            ->map(function ($deliverable) {
                $progress = (int) ($deliverable->progress ?? 0);
                $isOverdue = !$deliverable->completed_at && $deliverable->end_date && Carbon::parse($deliverable->end_date)->isPast() && $progress < 100;
                $startDate = $deliverable->start_date ? Carbon::parse($deliverable->start_date)->format('Y-m-d') : null;
                $endDate = $deliverable->end_date ? Carbon::parse($deliverable->end_date)->format('Y-m-d') : null;

                return [
                    'id' => (string) $deliverable->id,
                    'name' => $deliverable->title,
                    'title' => $deliverable->title,
                    'description' => $deliverable->description,
                    'start' => $startDate,
                    'end' => $endDate,
                    'progress' => $progress,
                    'grant' => $deliverable->grant_name,
                    'derived_status' => $progress >= 100 ? 'completed' : ($isOverdue ? 'overdue' : ($progress === 0 ? 'not_started' : 'ongoing')),
                    'custom_class' => 'deliverable-state-' . ($progress >= 100 ? 'completed' : ($isOverdue ? 'overdue' : ($progress === 0 ? 'not-started' : 'ongoing'))),
                ];
            })
            ->all();
    }

    private function fillStageSeries(array $series): array
    {
        return [
            'elementary' => $series['elementary'] ?? 0,
            'high_school' => $series['high_school'] ?? 0,
            'shs' => $series['shs'] ?? 0,
            'college' => $series['college'] ?? 0,
        ];
    }

    private function stageFromGradeLevel(string $gradeLevel): ?string
    {
        $normalized = Str::lower(trim($gradeLevel));

        return match ($normalized) {
            '1', '2', '3', '4', '5', '6' => 'elementary',
            '7', '8', '9', '10' => 'high_school',
            '11', '12' => 'shs',
            '1st year', '2nd year', '3rd year', '4th year', '5th year' => 'college',
            default => null,
        };
    }

    private function stageOrder(string $stage): int
    {
        return match ($stage) {
            'elementary' => 1,
            'high_school' => 2,
            'shs' => 3,
            'college' => 4,
            default => 0,
        };
    }

    private function stageCaseSql(string $column): string
    {
        return "CASE
            WHEN {$column} IN ('1','2','3','4','5','6') THEN 'elementary'
            WHEN {$column} IN ('7','8','9','10') THEN 'high_school'
            WHEN {$column} IN ('11','12') THEN 'shs'
            ELSE 'college'
        END";
    }

    private function latestEnrollmentSubQuery(Carbon $startDate, Carbon $endDate)
    {
        return DB::table('education_enrollments')
            ->select('beneficiary_id', DB::raw('MAX(id) as max_id'))
            ->where('academic_year_start_date', '<=', $endDate->toDateString())
            ->where('academic_year_end_date', '>=', $startDate->toDateString())
            ->groupBy('beneficiary_id');
    }

    private function applyAgeGroupFilter($query, string $ageGroup): void
    {
        $today = now()->startOfDay();

        match ($ageGroup) {
            'under_12' => $query->whereRaw('TIMESTAMPDIFF(YEAR, beneficiaries.birth_date, ?) < 12', [$today]),
            '12_15' => $query->whereRaw('TIMESTAMPDIFF(YEAR, beneficiaries.birth_date, ?) BETWEEN 12 AND 15', [$today]),
            '16_18' => $query->whereRaw('TIMESTAMPDIFF(YEAR, beneficiaries.birth_date, ?) BETWEEN 16 AND 18', [$today]),
            '19_plus' => $query->whereRaw('TIMESTAMPDIFF(YEAR, beneficiaries.birth_date, ?) >= 19', [$today]),
            default => null,
        };
    }

    private function activeStatusCounts(int $educationProgramId, int $sportsProgramId, ?Carbon $asOfDate = null): array
    {
        $cutoff = $asOfDate ? $asOfDate->toDateString() : now()->toDateString();

        $results = DB::table('beneficiaries')
            ->join('beneficiary_statuses as statuses', 'statuses.beneficiary_id', '=', 'beneficiaries.id')
            ->join('beneficiary_status_types as types', 'types.id', '=', 'statuses.beneficiary_status_type_id')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'beneficiaries.id')
            ->whereIn('memberships.program_id', [$educationProgramId, $sportsProgramId])
            ->where(function ($q) {
                $q->whereRaw('LOWER(types.status_name) LIKE ?', ['%scholar%'])
                  ->orWhereRaw('LOWER(types.status_name) LIKE ?', ['%registered player%']);
            })
            ->where(function ($q) use ($cutoff) {
                $q->whereNull('statuses.end_date')
                  ->orWhere('statuses.end_date', '>=', $cutoff);
            })
            ->selectRaw('memberships.program_id, types.status_name, COUNT(DISTINCT beneficiaries.id) as total')
            ->groupBy('memberships.program_id', 'types.status_name')
            ->get();

        $scholars = 0;
        $registeredPlayers = 0;

        foreach ($results as $row) {
            if ((int) $row->program_id === $educationProgramId && stripos($row->status_name, 'scholar') !== false) {
                $scholars += (int) $row->total;
            }
            if ((int) $row->program_id === $sportsProgramId && stripos($row->status_name, 'registered player') !== false) {
                $registeredPlayers += (int) $row->total;
            }
        }

        return [
            'scholars' => $scholars,
            'registeredPlayers' => $registeredPlayers,
        ];
    }

    private function scholarsExitedCount(int $programId, Carbon $startDate, Carbon $endDate): int
    {
        return (int) DB::table('beneficiary_statuses')
            ->join('beneficiary_status_types', 'beneficiary_status_types.id', '=', 'beneficiary_statuses.beneficiary_status_type_id')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'beneficiary_statuses.beneficiary_id')
            ->where('memberships.program_id', $programId)
            ->whereRaw('LOWER(beneficiary_status_types.status_name) LIKE ?', ['%scholar%'])
            ->whereNotNull('beneficiary_statuses.end_date')
            ->whereBetween('beneficiary_statuses.end_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->distinct('beneficiary_statuses.beneficiary_id')
            ->count('beneficiary_statuses.beneficiary_id');
    }

    private function activeScholarsList(int $programId, Carbon $endDate): array
    {
        $scholarIds = DB::table('beneficiaries')
            ->join('beneficiary_statuses as statuses', 'statuses.beneficiary_id', '=', 'beneficiaries.id')
            ->join('beneficiary_status_types as types', 'types.id', '=', 'statuses.beneficiary_status_type_id')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'beneficiaries.id')
            ->where('memberships.program_id', $programId)
            ->whereRaw('LOWER(types.status_name) LIKE ?', ['%scholar%'])
            ->where('statuses.start_date', '<=', $endDate->toDateString())
            ->where(function ($query) use ($endDate) {
                $query->whereNull('statuses.end_date')
                    ->orWhere('statuses.end_date', '>=', $endDate->toDateString());
            })
            ->distinct()
            ->pluck('beneficiaries.id');

        if ($scholarIds->isEmpty()) {
            return [];
        }

        $rows = DB::table('beneficiaries')
            ->whereIn('beneficiaries.id', $scholarIds)
            ->leftJoinSub(
                DB::table('education_enrollments')
                    ->select('beneficiary_id', DB::raw('MAX(id) as max_id'))
                    ->groupBy('beneficiary_id'),
                'latest_enr',
                fn($join) => $join->on('beneficiaries.id', '=', 'latest_enr.beneficiary_id')
            )
            ->leftJoin('education_enrollments as ee', 'ee.id', '=', 'latest_enr.max_id')
            ->select(
                'beneficiaries.first_name',
                'beneficiaries.last_name',
                'beneficiaries.sex',
                'ee.grade_level'
            )
            ->orderBy('beneficiaries.last_name')
            ->orderBy('beneficiaries.first_name')
            ->get();

        return $rows->map(fn($r) => [
            'name' => ($r->last_name ?? '') . ', ' . strtoupper(substr($r->first_name ?? '', 0, 1)) . '.',
            'grade_level' => $r->grade_level ?? 'N/A',
            'sex' => $r->sex ?? 'N/A',
        ])->all();
    }

    private function graduatesList(int $programId, Carbon $startDate, Carbon $endDate): array
    {
        $nonCollege = DB::table('education_enrollments')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'education_enrollments.beneficiary_id')
            ->join('beneficiaries', 'beneficiaries.id', '=', 'education_enrollments.beneficiary_id')
            ->where('memberships.program_id', $programId)
            ->whereBetween('education_enrollments.academic_year_start_date', [$startDate, $endDate])
            ->where('education_enrollments.enrollment_status', 'completed')
            ->where(function ($query) {
                $query->where('education_enrollments.grade_level', '6')
                    ->orWhere('education_enrollments.grade_level', '10')
                    ->orWhere('education_enrollments.grade_level', '12');
            })
            ->select(
                'beneficiaries.first_name',
                'beneficiaries.last_name',
                'beneficiaries.sex',
                'education_enrollments.grade_level'
            )
            ->distinct()
            ->get();

        $college = DB::table('education_enrollments as e')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'e.beneficiary_id')
            ->join('beneficiaries', 'beneficiaries.id', '=', 'e.beneficiary_id')
            ->joinSub(
                DB::table('education_enrollments')
                    ->select('beneficiary_id', DB::raw('MAX(academic_year_start_date) as max_date'))
                    ->where('education_level', 'college')
                    ->groupBy('beneficiary_id'),
                'latest',
                fn($join) => $join->on('e.beneficiary_id', '=', 'latest.beneficiary_id')
                    ->on('e.academic_year_start_date', '=', 'latest.max_date')
            )
            ->where('memberships.program_id', $programId)
            ->where('e.education_level', 'college')
            ->where('e.enrollment_status', 'completed')
            ->whereBetween('e.academic_year_start_date', [$startDate, $endDate])
            ->select(
                'beneficiaries.first_name',
                'beneficiaries.last_name',
                'beneficiaries.sex',
                'e.grade_level'
            )
            ->distinct()
            ->get();

        $all = $nonCollege->merge($college)
            ->sortBy('last_name')
            ->sortBy('first_name');

        return $all->map(fn($r) => [
            'name' => ($r->last_name ?? '') . ', ' . strtoupper(substr($r->first_name ?? '', 0, 1)) . '.',
            'grade_level' => $r->grade_level ?? 'N/A',
            'sex' => $r->sex ?? 'N/A',
        ])->values()->all();
    }

    private function exitedScholarsList(int $programId, Carbon $startDate, Carbon $endDate): array
    {
        $rows = DB::table('beneficiaries')
            ->join('beneficiary_statuses as statuses', 'statuses.beneficiary_id', '=', 'beneficiaries.id')
            ->join('beneficiary_status_types as types', 'types.id', '=', 'statuses.beneficiary_status_type_id')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'beneficiaries.id')
            ->where('memberships.program_id', $programId)
            ->whereRaw('LOWER(types.status_name) LIKE ?', ['%scholar%'])
            ->whereNotNull('statuses.end_date')
            ->whereBetween('statuses.end_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->distinct('beneficiaries.id')
            ->select('beneficiaries.id', 'beneficiaries.first_name', 'beneficiaries.last_name', 'beneficiaries.sex')
            ->get();

        if ($rows->isEmpty()) {
            return [];
        }

        $ids = $rows->pluck('id');
        $enrollments = DB::table('education_enrollments')
            ->whereIn('beneficiary_id', $ids)
            ->select('beneficiary_id', 'grade_level')
            ->whereIn('id', function ($q) use ($ids) {
                $q->selectRaw('MAX(id)')->from('education_enrollments')
                    ->whereIn('beneficiary_id', $ids)
                    ->groupBy('beneficiary_id');
            })
            ->get()
            ->keyBy('beneficiary_id');

        return $rows->map(fn($r) => [
            'name' => ($r->last_name ?? '') . ', ' . strtoupper(substr($r->first_name ?? '', 0, 1)) . '.',
            'grade_level' => ($enrollments->get($r->id)?->grade_level) ?? 'N/A',
            'sex' => $r->sex ?? 'N/A',
        ])->sortBy('name')->values()->all();
    }

    private function registeredPlayersList(int $programId, Carbon $endDate): array
    {
        $cutoff = $endDate->toDateString();

        $rows = DB::table('beneficiaries')
            ->join('beneficiary_statuses as statuses', 'statuses.beneficiary_id', '=', 'beneficiaries.id')
            ->join('beneficiary_status_types as types', 'types.id', '=', 'statuses.beneficiary_status_type_id')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'beneficiaries.id')
            ->where('memberships.program_id', $programId)
            ->whereRaw('LOWER(types.status_name) LIKE ?', ['%registered player%'])
            ->where('statuses.start_date', '<=', $cutoff)
            ->where(function ($query) use ($cutoff) {
                $query->whereNull('statuses.end_date')
                    ->orWhere('statuses.end_date', '>=', $cutoff);
            })
            ->distinct()
            ->select('beneficiaries.first_name', 'beneficiaries.last_name', 'beneficiaries.birth_date')
            ->orderBy('beneficiaries.last_name')
            ->orderBy('beneficiaries.first_name')
            ->get();

        return $rows->map(fn($r) => [
            'name' => ($r->last_name ?? '') . ', ' . strtoupper(substr($r->first_name ?? '', 0, 1)) . '.',
            'age' => $r->birth_date ? (int) Carbon::parse($r->birth_date)->diffInYears($endDate) : 'N/A',
        ])->all();
    }

    private function newDonorsList(?Program $programScope, Carbon $startDate, Carbon $endDate): array
    {
        return DB::table('donors')
            ->when($programScope, fn($q) => $q->whereExists(function ($sub) use ($programScope) {
                $sub->selectRaw('1')->from('donations')
                    ->whereColumn('donations.donor_id', 'donors.id')
                    ->where('donations.program_id', $programScope->id);
            }))
            ->whereBetween('donors.created_at', [$startDate, $endDate])
            ->select('donors.first_name', 'donors.last_name', 'donors.organization_name', 'donors.donor_type')
            ->orderBy('donors.created_at')
            ->get()
            ->map(fn($r) => [
                'name' => $r->donor_type === 'organization'
                    ? ($r->organization_name ?? 'N/A')
                    : (($r->last_name ?? '') . ', ' . strtoupper(substr($r->first_name ?? '', 0, 1)) . '.'),
                'type' => $r->donor_type === 'organization' ? 'Organization' : 'Individual',
            ])
            ->all();
    }

    private function grantsReceivedList(?Program $programScope, Carbon $startDate, Carbon $endDate): array
    {
        $rows = DB::table('grants')
            ->whereBetween('grants.start_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->when($programScope, fn($q) => $q->whereExists(function ($sub) use ($programScope) {
                $sub->selectRaw('1')->from('grant_program')
                    ->whereColumn('grant_program.grant_id', 'grants.id')
                    ->where('grant_program.program_id', $programScope->id);
            }))
            ->orderBy('grants.start_date')
            ->get(['id', 'organization_name', 'grant_name', 'total_amount']);

        if ($rows->isEmpty()) {
            return [];
        }

        $ids = $rows->pluck('id');

        $programMap = DB::table('grant_program')
            ->join('programs', 'programs.id', '=', 'grant_program.program_id')
            ->whereIn('grant_program.grant_id', $ids)
            ->select('grant_program.grant_id', 'programs.program_name')
            ->get()
            ->groupBy('grant_id')
            ->map(fn($items) => $items->pluck('program_name')->implode(', '));

        return $rows->map(fn($r) => [
            'organization_name' => $r->organization_name,
            'grant_name' => $r->grant_name,
            'program' => $programMap->get($r->id, 'N/A'),
            'amount' => (float) $r->total_amount,
        ])->all();
    }

    private function competitionsList(int $programId, Carbon $startDate, Carbon $endDate): array
    {
        $competitions = DB::table('competitions')
            ->join('competition_results', 'competition_results.competition_id', '=', 'competitions.id')
            ->where('competitions.program_id', $programId)
            ->whereBetween('competition_results.date_given', [$startDate->toDateString(), $endDate->toDateString()])
            ->select('competitions.id', 'competitions.name', 'competitions.start')
            ->distinct()
            ->orderBy('competitions.start')
            ->get();

        if ($competitions->isEmpty()) {
            return [];
        }

        $ids = $competitions->pluck('id');

        $results = DB::table('competition_results')
            ->join('beneficiaries', 'beneficiaries.id', '=', 'competition_results.beneficiary_id')
            ->whereIn('competition_results.competition_id', $ids)
            ->select(
                'competition_results.competition_id',
                'competition_results.placement',
                'beneficiaries.first_name',
                'beneficiaries.last_name'
            )
            ->orderBy('competition_results.placement')
            ->get()
            ->groupBy('competition_id');

        return $competitions->map(fn($c) => [
            'name' => $c->name,
            'date' => Carbon::parse($c->start)->format('M d, Y'),
            'results' => ($results->get($c->id) ?? collect())->map(fn($r) => [
                'name' => ($r->last_name ?? '') . ', ' . strtoupper(substr($r->first_name ?? '', 0, 1)) . '.',
                'result' => $r->placement,
            ])->all(),
        ])->all();
    }

    private function educationAttendanceTimeSeries(int $programId, Carbon $startDate, Carbon $endDate, string $period, ?string $gradeLevel): array
    {
        $buckets = $this->generateBuckets($startDate, $endDate, $period);
        $eqData = array_fill(0, count($buckets['labels']), 0);
        $tutorialData = array_fill(0, count($buckets['labels']), 0);
        [$rangeStart, $rangeEnd] = $this->dateRangeBounds($startDate, $endDate);

        $results = DB::table('attendances')
            ->join('activity_sessions', 'activity_sessions.id', '=', 'attendances.activity_session_id')
            ->join('activities', 'activities.id', '=', 'activity_sessions.activity_id')
            ->join('activity_types', 'activity_types.id', '=', 'activities.activity_type_id')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'attendances.beneficiary_id')
            ->when($gradeLevel, fn($q) => $q->join('education_enrollments', 'education_enrollments.beneficiary_id', '=', 'attendances.beneficiary_id')
                ->where('education_enrollments.grade_level', $gradeLevel))
            ->where('memberships.program_id', $programId)
            ->whereIn('activity_types.name', ['EQ Session', 'Tutorial Session'])
            ->whereBetween('activity_sessions.schedule', [$rangeStart, $rangeEnd])
            ->groupBy(DB::raw('DATE(activity_sessions.schedule)'), 'activity_types.name')
            ->selectRaw("DATE(activity_sessions.schedule) as date_col, activity_types.name as activity_type, COUNT(*) as total, SUM(CASE WHEN attendances.attendance_status = 'present' THEN 1 ELSE 0 END) as present")
            ->get();

        $grouped = $results->groupBy('activity_type');
        $eqResults = ($grouped->get('EQ Session') ?? collect())->keyBy('date_col');
        $tutorialResults = ($grouped->get('Tutorial Session') ?? collect())->keyBy('date_col');

        foreach ($buckets['ranges'] as $index => $range) {
            $eqPresent = 0; $eqTotal = 0;
            $tutPresent = 0; $tutTotal = 0;
            $cursor = $range['start']->copy();

            while ($cursor->lte($range['end'])) {
                $dateKey = $cursor->toDateString();
                $eqRow = $eqResults->get($dateKey);
                $tutRow = $tutorialResults->get($dateKey);
                if ($eqRow) { $eqPresent += (int) $eqRow->present; $eqTotal += (int) $eqRow->total; }
                if ($tutRow) { $tutPresent += (int) $tutRow->present; $tutTotal += (int) $tutRow->total; }
                $cursor->addDay();
            }

            $eqData[$index] = $eqTotal === 0 ? 0 : round(($eqPresent / $eqTotal) * 100, 2);
            $tutorialData[$index] = $tutTotal === 0 ? 0 : round(($tutPresent / $tutTotal) * 100, 2);
        }

        $yearlyEq = []; $yearlyTut = [];
        foreach ($results as $row) {
            $year = substr($row->date_col, 0, 4);
            if ($row->activity_type === 'EQ Session') {
                $yearlyEq[$year] ??= ['present' => 0, 'total' => 0];
                $yearlyEq[$year]['present'] += (int) $row->present;
                $yearlyEq[$year]['total'] += (int) $row->total;
            } else {
                $yearlyTut[$year] ??= ['present' => 0, 'total' => 0];
                $yearlyTut[$year]['present'] += (int) $row->present;
                $yearlyTut[$year]['total'] += (int) $row->total;
            }
        }
        $yearLabels = array_keys($yearlyEq + $yearlyTut);
        sort($yearLabels);
        $yearEqData = array_map(fn($y) => ($yearlyEq[$y]['total'] ?? 0) === 0 ? 0 : round(($yearlyEq[$y]['present'] / $yearlyEq[$y]['total']) * 100, 2), $yearLabels);
        $yearTutData = array_map(fn($y) => ($yearlyTut[$y]['total'] ?? 0) === 0 ? 0 : round(($yearlyTut[$y]['present'] / $yearlyTut[$y]['total']) * 100, 2), $yearLabels);

        return [
            'eqSessionAttendance' => ['labels' => $buckets['labels'], 'data' => $eqData],
            'tutorialSessionAttendance' => ['labels' => $buckets['labels'], 'data' => $tutorialData],
            'yearlyAggregates' => [
                'eqSessionAttendance' => ['labels' => $yearLabels, 'data' => $yearEqData],
                'tutorialSessionAttendance' => ['labels' => $yearLabels, 'data' => $yearTutData],
            ],
            'averages' => [
                'eq_session' => count($eqData) > 0 ? round(array_sum($eqData) / count($eqData), 2) : 0.0,
                'tutorial_session' => count($tutorialData) > 0 ? round(array_sum($tutorialData) / count($tutorialData), 2) : 0.0,
            ],
        ];
    }

    private function educationAttendanceTimeSeriesByYear(int $programId, Carbon $startDate, Carbon $endDate, ?string $gradeLevel): array
    {
        $labels = $this->yearLabelsInRange($startDate, $endDate);

        if ($labels === []) {
            return [
                'eqSessionAttendance' => ['labels' => [], 'data' => []],
                'tutorialSessionAttendance' => ['labels' => [], 'data' => []],
            ];
        }

        [$rangeStart, $rangeEnd] = $this->dateRangeBounds($startDate, $endDate);
        $eqData = array_fill(0, count($labels), 0);
        $tutorialData = array_fill(0, count($labels), 0);

        $results = DB::table('attendances')
            ->join('activity_sessions', 'activity_sessions.id', '=', 'attendances.activity_session_id')
            ->join('activities', 'activities.id', '=', 'activity_sessions.activity_id')
            ->join('activity_types', 'activity_types.id', '=', 'activities.activity_type_id')
            ->join('beneficiary_program_memberships as memberships', 'memberships.beneficiary_id', '=', 'attendances.beneficiary_id')
            ->when($gradeLevel, fn($q) => $q->join('education_enrollments', 'education_enrollments.beneficiary_id', '=', 'attendances.beneficiary_id')
                ->where('education_enrollments.grade_level', $gradeLevel))
            ->where('memberships.program_id', $programId)
            ->whereIn('activity_types.name', ['EQ Session', 'Tutorial Session'])
            ->whereBetween('activity_sessions.schedule', [$rangeStart, $rangeEnd])
            ->groupBy(DB::raw('YEAR(activity_sessions.schedule)'), 'activity_types.name')
            ->selectRaw('YEAR(activity_sessions.schedule) as year, activity_types.name as activity_type, COUNT(*) as total, SUM(CASE WHEN attendances.attendance_status = ? THEN 1 ELSE 0 END) as present', ['present'])
            ->get();

        $grouped = $results->groupBy('activity_type');

        foreach ($labels as $index => $yearLabel) {
            $year = (int) $yearLabel;

            foreach (['EQ Session', 'Tutorial Session'] as $type) {
                $typeResults = $grouped->get($type, collect());
                $row = $typeResults->firstWhere('year', $year);
                $total = $row ? (int) $row->total : 0;
                $present = $row ? (int) $row->present : 0;
                $value = $total === 0 ? 0 : round(($present / $total) * 100, 2);

                if ($type === 'EQ Session') {
                    $eqData[$index] = $value;
                } else {
                    $tutorialData[$index] = $value;
                }
            }
        }

        return [
            'eqSessionAttendance' => ['labels' => $labels, 'data' => $eqData],
            'tutorialSessionAttendance' => ['labels' => $labels, 'data' => $tutorialData],
        ];
    }

    public function buildReportPayload(ReportType $type, Carbon $startDate, Carbon $endDate, ?Program $program, string $periodType = 'custom'): array
    {
        $diffDays = (int) $startDate->diffInDays($endDate);

        $filters = [
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'period' => match (true) {
                $diffDays > 180 => 'year',
                $diffDays > 31  => 'week',
                default         => 'month',
            },
            'academicYear' => $startDate->year,
            'programId' => $program?->id,
            'gradeLevel' => null,
            'sportTypeId' => null,
            'ageGroup' => null,
        ];

        $payload = [
            'reportType' => $type->value,
            'dateRange' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],
        ];

        $educationProgram = null;

        if ($type === ReportType::Education || $type === ReportType::OrganizationalOverview) {
            $educationProgram = $program?->program_name === 'Education' ? $program : Program::find($this->programIdByName('Education'));
            if ($educationProgram) {
                $payload['education'] = $this->buildBeneficiaryMetrics($filters, $educationProgram);
                $payload['educationActivity'] = $this->buildActivityMetrics($filters, $educationProgram);
                $payload['education']['activeScholarsList'] = $this->activeScholarsList($educationProgram->id, $endDate);
                $payload['education']['graduatesList'] = $this->graduatesList($educationProgram->id, $startDate, $endDate);
                $payload['education']['exitedScholarsList'] = $this->exitedScholarsList($educationProgram->id, $startDate, $endDate);
            }
        }

        if ($type === ReportType::Sports || $type === ReportType::OrganizationalOverview) {
            $sportsProgram = $program?->program_name === 'Sports' ? $program : Program::find($this->programIdByName('Sports'));
            if ($sportsProgram) {
                $payload['sports'] = $this->buildBeneficiaryMetrics($filters, $sportsProgram);
                $payload['sportsActivity'] = $this->buildActivityMetrics($filters, $sportsProgram);
                $payload['sports']['registeredPlayersList'] = $this->registeredPlayersList($sportsProgram->id, $endDate);
                $payload['sports']['competitionsList'] = $this->competitionsList($sportsProgram->id, $startDate, $endDate);
            }
        }

        if ($type === ReportType::Funding || $type === ReportType::OrganizationalOverview) {
            $academicYear = $startDate->year;

            if ($program) {
                $metricService = app(\App\Services\DashboardMetricService::class);
                $fundsReceived = $metricService->getOrCompute(
                    $program->id, 'donations_received', null, $startDate->year, null,
                    fn() => $this->sumDonations($program, $startDate, $endDate),
                );
                $fundsAllocated = $metricService->getOrCompute(
                    $program->id, 'funds_allocated', null, $startDate->year, null,
                    fn() => $this->sumAllocations($program, $startDate, $endDate),
                );
                $fundingTarget = $metricService->getOrCompute(
                    $program->id, 'funding_target', null, $academicYear, null,
                    fn() => $this->sumFundingTargets($program, $academicYear),
                );
            } else {
                $fundsReceived = $this->sumDonations($program, $startDate, $endDate);
                $fundsAllocated = $this->sumAllocations($program, $startDate, $endDate);
                $fundingTarget = $this->sumFundingTargets($program, $academicYear);
            }

            $bucketInterval = $this->determineBucketInterval($startDate, $endDate, $periodType);
            $receivedData = $this->fundsReceivedByPeriod($program, $startDate, $endDate, $bucketInterval);
            $allocatedData = $this->fundsAllocatedByPeriod($program, $startDate, $endDate, $bucketInterval);

            $newDonors = DB::table('donors')
                ->when($program, fn($q) => $q->whereExists(function ($sub) use ($program) {
                    $sub->selectRaw('1')->from('donations')
                        ->whereColumn('donations.donor_id', 'donors.id')
                        ->where('donations.program_id', $program->id);
                }))
                ->whereBetween('donors.created_at', [$startDate, $endDate])
                ->count();

            $payload['funding'] = [
                'newDonors' => $newDonors,
                'grantsReceived' => $this->countGrants($program, $startDate, $endDate),
                'fundsReceived' => $fundsReceived,
                'fundsAllocated' => $fundsAllocated,
                'fundingTarget' => $fundingTarget,
                'receivedProgressPercent' => $fundingTarget > 0 ? min(100, round(($fundsReceived / $fundingTarget) * 100, 1)) : 0,
                'allocatedVsReceivedPercent' => $fundsReceived > 0 ? min(100, round(($fundsAllocated / $fundsReceived) * 100, 1)) : 0,
                'trendPeriod' => $bucketInterval,
                'trendLabels' => $receivedData['labels'],
                'trendReceived' => $receivedData['values'],
                'trendAllocated' => $allocatedData['values'],
            ];

            $payload['funding']['newDonorsList'] = $this->newDonorsList($program, $startDate, $endDate);
            $payload['funding']['grantsReceivedList'] = $this->grantsReceivedList($program, $startDate, $endDate);
        }

        if ($diffDays > 365) {
            $years = $this->yearLabelsInRange($startDate, $endDate);

            if (isset($payload['education'])) {
                $edu = &$payload['education'];
                $educationBreakdowns = $this->educationBreakdownsByYears($educationProgram->id, $years);

                $aggregatedGraduates = $this->graduatesByStageAggregate($educationProgram->id, $startDate, $endDate);
                $aggregatedTransitionSuccess = [];
                $aggregatedTransitionTotal = [];
                $aggregatedCompletionCompleted = [];
                $aggregatedCompletionTotal = [];

                foreach ($years as $year) {
                    $yearInt = (int) $year;

                    $yearTransition = $educationBreakdowns['transition'][$yearInt] ?? [];
                    foreach ($yearTransition as $stage => $values) {
                        $aggregatedTransitionSuccess[$stage] = ($aggregatedTransitionSuccess[$stage] ?? 0) + ($values['success'] ?? 0);
                        $aggregatedTransitionTotal[$stage] = ($aggregatedTransitionTotal[$stage] ?? 0) + ($values['total'] ?? 0);
                    }

                    $yearCompletion = $educationBreakdowns['completion'][$yearInt] ?? [];
                    foreach ($yearCompletion as $stage => $values) {
                        $aggregatedCompletionCompleted[$stage] = ($aggregatedCompletionCompleted[$stage] ?? 0) + ($values['completed'] ?? 0);
                        $aggregatedCompletionTotal[$stage] = ($aggregatedCompletionTotal[$stage] ?? 0) + ($values['total'] ?? 0);
                    }
                }

                $edu['graduatesByStage'] = $aggregatedGraduates;
                $edu['graduatesTotal'] = array_sum($aggregatedGraduates);

                $aggregatedRates = [];
                foreach ($aggregatedTransitionTotal as $stage => $total) {
                    if ($total > 0) {
                        $aggregatedRates[$stage] = round(($aggregatedTransitionSuccess[$stage] / $total) * 100, 2);
                    }
                }
                $edu['transitionRates'] = $aggregatedRates;
                $edu['transitionRateBreakdown'] = [
                    'rates' => $aggregatedRates,
                    'successCounts' => $aggregatedTransitionSuccess,
                    'totalCounts' => $aggregatedTransitionTotal,
                    'academicYear' => $startDate->year,
                ];

                $aggregatedCompletionRates = [];
                foreach ($aggregatedCompletionTotal as $stage => $total) {
                    if ($total > 0) {
                        $aggregatedCompletionRates[$stage] = round(($aggregatedCompletionCompleted[$stage] / $total) * 100, 2);
                    }
                }
                $edu['completionRates'] = $aggregatedCompletionRates;
                $edu['completionRateBreakdown'] = [
                    'rates' => $aggregatedCompletionRates,
                    'completedCounts' => $aggregatedCompletionCompleted,
                    'totalCounts' => $aggregatedCompletionTotal,
                    'academicYear' => $startDate->year,
                ];
            }

            if (isset($payload['funding'])) {
                $fnd = &$payload['funding'];

                $totalTarget = $this->sumFundingTargetsForYears($program, array_map('intval', $years));

                $fnd['fundingTarget'] = $totalTarget;
                $fnd['receivedProgressPercent'] = $totalTarget > 0 ? min(100, round(($fnd['fundsReceived'] / $totalTarget) * 100, 1)) : 0;

                $multiBucket = $this->determineBucketInterval($startDate, $endDate, $periodType);
                $multiReceived = $this->fundsReceivedByPeriod($program, $startDate, $endDate, $multiBucket);
                $multiAllocated = $this->fundsAllocatedByPeriod($program, $startDate, $endDate, $multiBucket);
                $fnd['trendPeriod'] = $multiBucket;
                $fnd['trendLabels'] = $multiReceived['labels'];
                $fnd['trendReceived'] = $multiReceived['values'];
                $fnd['trendAllocated'] = $multiAllocated['values'];
            }

            foreach (['educationActivity', 'sportsActivity'] as $key) {
                if (!isset($payload[$key])) continue;

                if (isset($payload[$key]['attendanceTrendsByYear']['eqSessionAttendance'])) {
                    $payload[$key]['trends']['eqSessionAttendance'] = $payload[$key]['attendanceTrendsByYear']['eqSessionAttendance'];
                }
                if (isset($payload[$key]['attendanceTrendsByYear']['tutorialSessionAttendance'])) {
                    $payload[$key]['trends']['tutorialSessionAttendance'] = $payload[$key]['attendanceTrendsByYear']['tutorialSessionAttendance'];
                }
                if (isset($payload[$key]['attendanceTrendsByYear']['trainingSessionAttendance'])) {
                    $payload[$key]['trends']['trainingSessionAttendance'] = $payload[$key]['attendanceTrendsByYear']['trainingSessionAttendance'];
                }
                if (isset($payload[$key]['uniqueVisitsByYear'])) {
                    $payload[$key]['trends']['uniqueVisits'] = $payload[$key]['uniqueVisitsByYear'];
                }
            }
        }

        return $payload;
    }

    private function determineBucketInterval(Carbon $startDate, Carbon $endDate, string $period = 'custom'): string
    {
        $diffDays = (int) $startDate->diffInDays($endDate);

        return match ($period) {
            'week'       => 'daily',
            'month'      => 'weekly',
            'year'       => 'monthly',
            'quarterly'  => 'monthly',
            'annual'     => 'monthly',
            'multi_year' => 'yearly',
            'custom'     => $diffDays > 365 ? 'yearly' : 'monthly',
            default      => 'monthly',
        };
    }

    private function generateBucketDates(Carbon $startDate, Carbon $endDate, string $interval): array
    {
        $dates = [];
        $current = $startDate->copy()->startOfDay();

        while ($current <= $endDate) {
            $bucketDate = match ($interval) {
                'daily'   => $current->copy(),
                'weekly'  => $current->copy()->startOfWeek(),
                'monthly' => $current->copy()->startOfMonth(),
                'yearly'  => $current->copy()->startOfYear(),
            };
            $key = $bucketDate->toDateString();
            if (!isset($dates[$key])) {
                $dates[$key] = $bucketDate;
            }
            $current = match ($interval) {
                'daily'   => $current->addDay(),
                'weekly'  => $current->addWeek(),
                'monthly' => $current->addMonth(),
                'yearly'  => $current->addYear(),
            };
        }

        return $dates;
    }

    private function formatBucketLabel(Carbon $date, string $interval): string
    {
        return match ($interval) {
            'daily'   => $date->format('M j'),
            'weekly'  => $date->format('M j, Y'),
            'monthly' => $date->format('M Y'),
            'yearly'  => $date->format('Y'),
        };
    }

    private function sqlGroupExpr(string $dateColumn, string $interval): string
    {
        return match ($interval) {
            'daily'   => "DATE({$dateColumn})",
            'weekly'  => "DATE(DATE_SUB({$dateColumn}, INTERVAL WEEKDAY({$dateColumn}) DAY))",
            'monthly' => "DATE_FORMAT({$dateColumn}, '%Y-%m-01')",
            'yearly'  => "DATE_FORMAT({$dateColumn}, '%Y-01-01')",
        };
    }

    private function fundsReceivedByPeriod(?Program $programScope, Carbon $startDate, Carbon $endDate, string $bucketInterval): array
    {
        $bucketDates = $this->generateBucketDates($startDate, $endDate, $bucketInterval);
        $bucketKeys = array_keys($bucketDates);

        $donExpr = $this->sqlGroupExpr('transaction_date', $bucketInterval);
        $donationTotals = DB::table('donations')
            ->selectRaw("{$donExpr} as bucket, SUM(amount) as total")
            ->where('status', 'completed')
            ->whereBetween('transaction_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->when($programScope, fn($q) => $q->where('program_id', $programScope->id))
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->pluck('total', 'bucket')
            ->all();

        $grantExpr = $this->sqlGroupExpr('start_date', $bucketInterval);
        $grantTotals = DB::table('grants')
            ->selectRaw("{$grantExpr} as bucket, SUM(total_amount) as total")
            ->whereBetween('start_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->when($programScope, fn($q) => $q->whereExists(function ($sub) use ($programScope) {
                $sub->selectRaw('1')->from('grant_program')
                    ->whereColumn('grant_program.grant_id', 'grants.id')
                    ->where('grant_program.program_id', $programScope->id);
            }))
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->pluck('total', 'bucket')
            ->all();

        $merged = [];
        foreach ($bucketKeys as $key) {
            $merged[$key] = 0.0;
        }
        foreach ($donationTotals as $key => $total) {
            $merged[$key] = ($merged[$key] ?? 0) + (float) $total;
        }
        foreach ($grantTotals as $key => $total) {
            $merged[$key] = ($merged[$key] ?? 0) + (float) $total;
        }
        ksort($merged);

        $labels = [];
        $values = [];
        foreach ($merged as $key => $value) {
            $labels[] = $this->formatBucketLabel($bucketDates[$key], $bucketInterval);
            $values[] = $value;
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function fundsAllocatedByPeriod(?Program $programScope, Carbon $startDate, Carbon $endDate, string $bucketInterval): array
    {
        $bucketDates = $this->generateBucketDates($startDate, $endDate, $bucketInterval);
        $bucketKeys = array_keys($bucketDates);

        $allExpr = $this->sqlGroupExpr('date_allocated', $bucketInterval);
        $totals = DB::table('allocations')
            ->selectRaw("{$allExpr} as bucket, SUM(amount_cents / 100) as total")
            ->whereBetween('date_allocated', [$startDate->toDateString(), $endDate->toDateString()])
            ->when($programScope, fn($q) => $q->whereExists(function ($sub) use ($programScope) {
                $sub->selectRaw('1')->from('beneficiary_program_memberships')
                    ->whereColumn('beneficiary_program_memberships.beneficiary_id', 'allocations.beneficiary_id')
                    ->where('beneficiary_program_memberships.program_id', $programScope->id);
            }))
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->pluck('total', 'bucket')
            ->all();

        $merged = [];
        foreach ($bucketKeys as $key) {
            $merged[$key] = 0.0;
        }
        foreach ($totals as $key => $total) {
            $merged[$key] = ($merged[$key] ?? 0) + (float) $total;
        }
        ksort($merged);

        $labels = [];
        $values = [];
        foreach ($merged as $key => $value) {
            $labels[] = $this->formatBucketLabel($bucketDates[$key], $bucketInterval);
            $values[] = $value;
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function resolveMetricLabelsData(
        string $metricKey,
        int $programId,
        Carbon $startDate,
        Carbon $endDate,
        array $expectedLabels,
        \Closure $computation,
    ): array {
        $year = $startDate->year;

        if (!$this->coversCompleteYear($startDate, $endDate, $year)) {
            return $computation();
        }

        $metricService = app(\App\Services\DashboardMetricService::class);
        $dimensions = array_map(fn(string $l) => Str::snake(Str::lower($l)), $expectedLabels);

        $values = $metricService->getSet($programId, $metricKey, $year, null, $dimensions);
        if ($values !== null) {
            return [
                'labels' => $expectedLabels,
                'data' => array_map(fn(string $d) => (float) ($values[$d] ?? 0), $dimensions),
            ];
        }

        $result = $computation();

        if (isset($result['labels'], $result['data'])) {
            $set = [];
            foreach ($result['labels'] as $i => $label) {
                $dim = Str::snake(Str::lower($label));
                $set[$dim] = (float) ($result['data'][$i] ?? 0);
            }
            $metricService->setSet($programId, $metricKey, $year, null, $set);
        }

        return $result;
    }

    private function resolveMetricKeyedSet(
        string $metricKey,
        int $programId,
        Carbon $startDate,
        Carbon $endDate,
        array $expectedKeys,
        \Closure $computation,
    ): array {
        $year = $startDate->year;

        if (!$this->coversCompleteYear($startDate, $endDate, $year)) {
            return $computation();
        }

        $metricService = app(\App\Services\DashboardMetricService::class);

        $values = $metricService->getSet($programId, $metricKey, $year, null, $expectedKeys);
        if ($values !== null) {
            return $values;
        }

        $result = $computation();

        $set = [];
        foreach ($expectedKeys as $key) {
            $set[$key] = (float) ($result[$key] ?? 0);
        }
        $metricService->setSet($programId, $metricKey, $year, null, $set);

        return $result;
    }

    private function coversCompleteYear(Carbon $startDate, Carbon $endDate, ?int $year = null): bool
    {
        $year ??= $startDate->year;

        return $startDate->isSameDay(Carbon::create($year, 1, 1))
            && $endDate->isSameDay(Carbon::create($year, 12, 31));
    }

    public function collectMetricsForSummary(int $programId, int $year, ?int $month = null): array
    {
        $metrics = [];
        $program = Program::find($programId);
        if (!$program) {
            return $metrics;
        }

        $startDate = Carbon::create($year, $month ?? 1, 1)->startOfDay();
        $endDate = $month
            ? Carbon::create($year, $month, 1)->endOfMonth()->endOfDay()
            : Carbon::create($year, 12, 31)->endOfDay();

        $programName = Str::lower($program->program_name);

        if ($programName === 'education') {
            $seCategoryId = $this->socioEmotionalCategoryId();

            $scholars = $this->scholarCountsByEducationalStage($programId, $startDate, $endDate);
            foreach ($scholars['labels'] as $i => $stage) {
                $metrics[] = [
                    'program_id' => $programId,
                    'metric' => 'active_scholars',
                    'dimension' => Str::snake(Str::lower($stage)),
                    'year' => $year,
                    'month' => $month,
                    'value' => (float) ($scholars['data'][$i] ?? 0),
                ];
            }

            $graduates = $this->graduatesByStage($programId, $year);
            foreach ($graduates as $stage => $count) {
                if (is_numeric($count)) {
                    $metrics[] = [
                        'program_id' => $programId,
                        'metric' => 'graduates',
                        'dimension' => $stage,
                        'year' => $year,
                        'month' => null,
                        'value' => (float) $count,
                    ];
                }
            }

            $grades = $this->averageGradesByStage($programId, $startDate, $endDate);
            foreach ($grades as $stage => $value) {
                if (is_numeric($value)) {
                    $metrics[] = [
                        'program_id' => $programId,
                        'metric' => 'avg_grade',
                        'dimension' => $stage,
                        'year' => $year,
                        'month' => $month,
                        'value' => (float) $value,
                    ];
                }
            }

            if ($seCategoryId) {
                $seScores = $this->averageSocioEmotionalByStage($programId, $startDate, $endDate, $seCategoryId);
                foreach ($seScores as $stage => $value) {
                    if (is_numeric($value)) {
                        $metrics[] = [
                            'program_id' => $programId,
                            'metric' => 'avg_socio_emotional',
                            'dimension' => $stage,
                            'year' => $year,
                            'month' => $month,
                            'value' => (float) $value,
                        ];
                    }
                }
            }
        }

        if ($programName === 'sports') {
            $players = $this->newPlayerCountsBySportsType($programId, $startDate, $endDate);
            foreach ($players['labels'] as $i => $sportType) {
                $metrics[] = [
                    'program_id' => $programId,
                    'metric' => 'new_players',
                    'dimension' => Str::snake(Str::lower($sportType)),
                    'year' => $year,
                    'month' => $month,
                    'value' => (float) ($players['data'][$i] ?? 0),
                ];
            }
        }

        $metrics[] = [
            'program_id' => $programId,
            'metric' => 'donations_received',
            'dimension' => null,
            'year' => $year,
            'month' => $month,
            'value' => $this->sumDonations($program, $startDate, $endDate),
        ];

        $metrics[] = [
            'program_id' => $programId,
            'metric' => 'funds_allocated',
            'dimension' => null,
            'year' => $year,
            'month' => $month,
            'value' => $this->sumAllocations($program, $startDate, $endDate),
        ];

        return $metrics;
    }
}