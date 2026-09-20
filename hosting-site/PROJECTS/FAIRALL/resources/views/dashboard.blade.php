<x-dashboardlayout name="{{ $name }}" title="Dashboard">
    @php
        $showProgramFilter = $showProgramFilter ?? false;
        $programOptions = $programOptions ?? [];
        $selectedProgramId = $selectedProgramId ?? null;
        $activeSection = $activeSection ?? 'beneficiary';
        $filters = $filters ?? [];
        $sectionVisibility = $sectionVisibility ?? [];
        $beneficiaryMetrics = $beneficiaryMetrics ?? [];
        $activityMetrics = $activityMetrics ?? [];
        $donorMetrics = $donorMetrics ?? [];
        $deliverablesGanttTasks = $donorMetrics['deliverablesGanttTasks'] ?? [];
        $dateRangeStart = \Carbon\Carbon::parse($filters['startDate'] ?? now()->subDays(29)->toDateString());
        $dateRangeEnd = \Carbon\Carbon::parse($filters['endDate'] ?? now()->toDateString());
        $canShowEducation = is_null($selectedProgramName) || strtolower($selectedProgramName) === 'education';
        $canShowSports = is_null($selectedProgramName) || strtolower($selectedProgramName) === 'sports';

        $isCurrentYearRange = ($filters['period'] ?? 'year') === 'year' && (int) ($filters['academicYear'] ?? now()->year) === (int) now()->year;
        $currentMonthIndex = now()->month;

        $trendLabels = $donorMetrics['trendLabels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $trendReceivedData = $donorMetrics['trendReceived'] ?? array_fill(0, 12, 0);
        $trendAllocatedData = $donorMetrics['trendAllocated'] ?? array_fill(0, 12, 0);
        $trendPeriod = $donorMetrics['trendPeriod'] ?? 'monthly';
        $trendPeriodLabel = match ($trendPeriod) {
            'daily' => 'Day',
            'weekly' => 'Week',
            'monthly' => 'Month',
            'yearly' => 'Year',
            default => 'Month',
        };

        $fundsTrendConfig = [
            'type' => 'bar',
            'data' => [
                'labels' => $trendLabels,
                'datasets' => [
                    [
                        'label' => 'Received',
                        'data' => $trendReceivedData,
                        'backgroundColor' => 'rgba(255, 204, 51, 1)',
                        'borderColor' => 'rgba(255, 204, 51, 1)',
                        'borderWidth' => 1,
                        'borderRadius' => 4,
                    ],
                    [
                        'label' => 'Allocated',
                        'data' => $trendAllocatedData,
                        'backgroundColor' => 'rgba(16, 185, 129, 1)',
                        'borderColor' => 'rgba(16, 185, 129, 1)',
                        'borderWidth' => 1,
                        'borderRadius' => 4,
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'position' => 'bottom',
                        'labels' => ['color' => 'rgba(255,255,255,0.6)'],
                    ],
                ],
                'scales' => [
                    'x' => [
                        'grid' => ['color' => 'rgba(255,255,255,0.06)'],
                        'ticks' => ['color' => 'rgba(255,255,255,0.5)'],
                    ],
                    'y' => [
                        'beginAtZero' => true,
                        'grid' => ['color' => 'rgba(255,255,255,0.06)'],
                        'ticks' => ['color' => 'rgba(255,255,255,0.5)'],
                    ],
                ],
            ],
        ];

        $activityTrends = $activityMetrics['trends'] ?? [];
        $activityYearTrends = $activityMetrics['attendanceTrendsByYear'] ?? [];
        $uniqueVisitsByYear = $activityMetrics['uniqueVisitsByYear'] ?? [];
        $useActivityYearSeries = $dateRangeStart->year !== $dateRangeEnd->year;

        $attendanceTrendLabels = $useActivityYearSeries
            ? (!empty($activityYearTrends['eqSessionAttendance']['labels'])
                ? $activityYearTrends['eqSessionAttendance']['labels']
                : $activityYearTrends['trainingSessionAttendance']['labels'] ?? [])
            : (!empty($activityTrends['eqSessionAttendance']['labels'])
                ? $activityTrends['eqSessionAttendance']['labels']
                : $activityTrends['trainingSessionAttendance']['labels'] ?? []);
        $uniqueVisitsTrendLabels = $useActivityYearSeries
            ? $uniqueVisitsByYear['labels'] ?? []
            : $activityTrends['uniqueVisits']['labels'] ?? [];

        if (!$useActivityYearSeries && $isCurrentYearRange) {
            $currentYearSeriesLength = min(count($attendanceTrendLabels), count($uniqueVisitsTrendLabels), $currentMonthIndex);
            $attendanceTrendLabels = array_slice($attendanceTrendLabels, 0, $currentYearSeriesLength);
            $uniqueVisitsTrendLabels = array_slice($uniqueVisitsTrendLabels, 0, $currentYearSeriesLength);
            $attendanceTrendEducationData = array_slice($activityTrends['eqSessionAttendance']['data'] ?? [], 0, $currentYearSeriesLength);
            $attendanceTrendTutorialData = array_slice($activityTrends['tutorialSessionAttendance']['data'] ?? [], 0, $currentYearSeriesLength);
            $attendanceTrendTrainingData = array_slice($activityTrends['trainingSessionAttendance']['data'] ?? [], 0, $currentYearSeriesLength);
            $uniqueVisitsTrendData = array_slice($activityTrends['uniqueVisits']['data'] ?? [], 0, $currentYearSeriesLength);
        } elseif ($useActivityYearSeries) {
            $attendanceTrendEducationData = $activityYearTrends['eqSessionAttendance']['data'] ?? [];
            $attendanceTrendTutorialData = $activityYearTrends['tutorialSessionAttendance']['data'] ?? [];
            $attendanceTrendTrainingData = $activityYearTrends['trainingSessionAttendance']['data'] ?? [];
            $uniqueVisitsTrendData = $uniqueVisitsByYear['data'] ?? [];
        } else {
            $attendanceTrendEducationData = $activityTrends['eqSessionAttendance']['data'] ?? [];
            $attendanceTrendTutorialData = $activityTrends['tutorialSessionAttendance']['data'] ?? [];
            $attendanceTrendTrainingData = $activityTrends['trainingSessionAttendance']['data'] ?? [];
            $uniqueVisitsTrendData = $activityTrends['uniqueVisits']['data'] ?? [];
        }

        if ($useActivityYearSeries) {
            $attendanceTrendConfig = [
                'type' => 'line',
                'data' => [
                    'labels' => $attendanceTrendLabels,
                    'datasets' => array_values(
                        array_filter([
                            $canShowEducation
                                ? [
                                    'label' => 'EQ Session Attendance %',
                                    'data' => $attendanceTrendEducationData,
                                    'borderColor' => 'rgba(255, 204, 51, 0.95)',
                                    'backgroundColor' => 'rgba(255, 204, 51, 0.12)',
                                    'tension' => 0.25,
                                ]
                                : null,
                            $canShowEducation
                                ? [
                                    'label' => 'Tutorial Session Attendance %',
                                    'data' => $attendanceTrendTutorialData,
                                    'borderColor' => 'rgba(59, 130, 246, 0.95)',
                                    'backgroundColor' => 'rgba(59, 130, 246, 0.12)',
                                    'tension' => 0.25,
                                ]
                                : null,
                            $canShowSports
                                ? [
                                    'label' => 'Training Session Attendance %',
                                    'data' => $attendanceTrendTrainingData,
                                    'borderColor' => 'rgba(16, 185, 129, 0.95)',
                                    'backgroundColor' => 'rgba(16, 185, 129, 0.12)',
                                    'tension' => 0.25,
                                ]
                                : null,
                        ]),
                    ),
                ],
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'plugins' => ['legend' => ['position' => 'bottom']],
                    'scales' => [
                        'x' => ['title' => ['display' => true, 'text' => 'Year']],
                    ],
                ],
            ];

            $uniqueVisitsTrendConfig = [
                'type' => 'line',
                'data' => [
                    'labels' => $uniqueVisitsTrendLabels,
                    'datasets' => [
                        [
                            'label' => 'Unique Visits',
                            'data' => $uniqueVisitsTrendData,
                            'borderColor' => 'rgba(244, 114, 182, 0.95)',
                            'backgroundColor' => 'rgba(244, 114, 182, 0.12)',
                            'tension' => 0.25,
                            'fill' => true,
                        ],
                    ],
                ],
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'plugins' => ['legend' => ['display' => false]],
                    'scales' => [
                        'x' => ['title' => ['display' => true, 'text' => 'Year']],
                        'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
                    ],
                ],
            ];
        } else {
            $attendanceTrendConfig = [
                'type' => 'line',
                'data' => [
                    'labels' => $attendanceTrendLabels,
                    'datasets' => array_values(
                        array_filter([
                            $canShowEducation
                                ? [
                                    'label' => 'EQ Session Attendance %',
                                    'data' => $attendanceTrendEducationData,
                                    'borderColor' => 'rgba(255, 204, 51, 0.95)',
                                    'backgroundColor' => 'rgba(255, 204, 51, 0.12)',
                                    'tension' => 0.25,
                                ]
                                : null,
                            $canShowEducation
                                ? [
                                    'label' => 'Tutorial Session Attendance %',
                                    'data' => $attendanceTrendTutorialData,
                                    'borderColor' => 'rgba(59, 130, 246, 0.95)',
                                    'backgroundColor' => 'rgba(59, 130, 246, 0.12)',
                                    'tension' => 0.25,
                                ]
                                : null,
                            $canShowSports
                                ? [
                                    'label' => 'Training Session Attendance %',
                                    'data' => $attendanceTrendTrainingData,
                                    'borderColor' => 'rgba(16, 185, 129, 0.95)',
                                    'backgroundColor' => 'rgba(16, 185, 129, 0.12)',
                                    'tension' => 0.25,
                                ]
                                : null,
                        ]),
                    ),
                ],
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'plugins' => ['legend' => ['position' => 'bottom']],
                ],
            ];

            $uniqueVisitsTrendConfig = [
                'type' => 'line',
                'data' => [
                    'labels' => $uniqueVisitsTrendLabels,
                    'datasets' => [
                        [
                            'label' => 'Unique Visits',
                            'data' => $uniqueVisitsTrendData,
                            'borderColor' => 'rgba(244, 114, 182, 0.95)',
                            'backgroundColor' => 'rgba(244, 114, 182, 0.12)',
                            'tension' => 0.25,
                            'fill' => true,
                        ],
                    ],
                ],
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'plugins' => ['legend' => ['display' => false]],
                    'scales' => [
                        'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
                    ],
                ],
            ];
        }
        $averageSocioEmotionalByStage = $beneficiaryMetrics['averageSocioEmotionalByStage'] ?? [];
        $selectedPeriod = $filters['period'] ?? 'year';
        $dashboardRouteName = match ($activeSection) {
            'activity' => 'dashboard.activity',
            'donor' => 'dashboard.donor',
            default => 'dashboard.beneficiary',
        };
        $tabQuery = array_filter(
            [
                'period' => $selectedPeriod,
                'from' => $filters['period'] === 'custom' ? $filters['startDate'] ?? null : null,
                'to' => $filters['period'] === 'custom' ? $filters['endDate'] ?? null : null,
                'year' => $filters['period'] === 'year' ? $filters['academicYear'] ?? now()->year : null,
                'program_id' => $selectedProgramId,
            ],
            fn($value) => !is_null($value) && $value !== '',
        );
    @endphp

    <div class="mb-8 space-y-6">
        <div>
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold mb-2"
                        style="font-family: var(--font-header1); color: white;">
                        Welcome back, {{ $name }}!
                    </h1>
                    <p class="text-white/70 max-w-3xl" style="font-family: var(--font-body1);">Metrics grouped
                        by
                        beneficiary management, activity management & attendance tracking, and donor & grant management.
                    </p>
                </div>
                @can('manage-reports')
                    @php
                        $reportQuota = $reportQuota ?? null;
                        $reportIsExempt = $reportQuota['is_exempt'] ?? $reportQuota['isExempt'] ?? false;
                        $reportRemaining = $reportQuota['remaining'] ?? 5;
                        $reportLimit = $reportQuota['limit'] ?? 5;
                        $reportResetAt = $reportQuota['resetAt'] ?? null;
                        $reportIsLimited = !$reportIsExempt && $reportRemaining <= 0;
                        $reportTooltip = $reportIsExempt
                            ? 'Unlimited — system admin'
                            : ($reportIsLimited
                                ? 'Limit reached (5/5) — resets ' . ($reportResetAt ? \Carbon\Carbon::parse($reportResetAt)->diffForHumans() : 'in 24 hours')
                                : ($reportRemaining . '/' . $reportLimit . ' reports left'));
                    @endphp
                    <div class="flex flex-col items-end gap-2">
                        <div class="tooltip tooltip-left tooltip-warning" data-tip="{{ $reportTooltip }}">
                            <button type="button"
                                @if(!$reportIsLimited) onclick="reportModal.showModal()" @endif
                                @if($reportIsLimited) disabled @endif
                                class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-semibold transition-all duration-300 whitespace-nowrap {{ $reportIsLimited ? 'opacity-50 cursor-not-allowed' : 'hover:scale-105 hover:shadow-lg' }}"
                                style="background-color: var(--color-accent1); color: var(--color-primary1); font-family: var(--font-body1);"
                                @if($reportIsLimited) aria-disabled="true" title="{{ $reportTooltip }}" @endif>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                                </svg>
                                Generate Report
                            </button>
                        </div>
                        @if($reportIsExempt)
                            <p class="text-xs text-white/50" style="font-family: var(--font-body1);">
                                Unlimited access
                            </p>
                        @elseif($reportIsLimited)
                            <p class="text-xs text-amber-200 max-w-[260px] text-right" style="font-family: var(--font-body1);">
                                Daily limit reached (5/5). Resets {{ $reportResetAt ? \Carbon\Carbon::parse($reportResetAt)->diffForHumans() : 'in 24 hours' }}.
                            </p>
                        @else
                            <p class="text-xs text-white/50" style="font-family: var(--font-body1);">
                                {{ $reportRemaining }}/{{ $reportLimit }} daily reports left
                            </p>
                        @endif
                    </div>
                    @include('reports.modal')
                @endcan
            </div>
            <div class="w-16 h-1 mt-3 rounded-full" style="background-color: var(--color-accent1);"></div>
            <div
                class="mt-4 inline-flex items-center gap-2 rounded-full border border-emerald-300/20 bg-emerald-500/10 px-4 py-2 text-sm text-emerald-100">
                <span class="font-semibold">Current range:</span>
                <span>{{ $dateRangeStart->format('M d, Y') }} to {{ $dateRangeEnd->format('M d, Y') }}</span>
            </div>
        </div>

        <form method="GET" action="{{ route($dashboardRouteName, $tabQuery) }}" data-dashboard-period-form
            class="rounded-2xl mb-6 p-4"
            style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
            <div class="grid gap-3 md:grid-cols-{{ $showProgramFilter ? '4' : '3' }}">
                <div>
                    <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider"
                        style="color: rgba(255,255,255,0.5);">Period</label>
                    <select name="period" data-period-select class="w-full rounded-xl px-4 py-2 text-sm"
                        style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">
                        @foreach (['week' => 'Week', 'month' => 'Month', 'year' => 'Year', 'custom' => 'Custom'] as $value => $label)
                            <option value="{{ $value }}" style="background: var(--color-primary1);"
                                @selected(($filters['period'] ?? 'year') === $value)>{{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="{{ $selectedPeriod === 'custom' ? '' : 'hidden' }}" data-period-field="custom">
                    <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider"
                        style="color: rgba(255,255,255,0.5);">From</label>
                    <input type="date" name="from"
                        value="{{ $filters['startDate'] ?? now()->subDays(29)->toDateString() }}"
                        @disabled($selectedPeriod !== 'custom') class="w-full rounded-xl px-4 py-2 text-sm text-primary1"
                        style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                </div>

                <div class="{{ $selectedPeriod === 'custom' ? '' : 'hidden' }}" data-period-field="custom">
                    <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider"
                        style="color: rgba(255,255,255,0.5);">To</label>
                    <input type="date" name="to" value="{{ $filters['endDate'] ?? now()->toDateString() }}"
                        @disabled($selectedPeriod !== 'custom') class="w-full rounded-xl px-4 py-2 text-sm text-primary1"
                        style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                </div>

                <div class="{{ $selectedPeriod === 'year' ? '' : 'hidden' }}" data-period-field="year">
                    <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider"
                        style="color: rgba(255,255,255,0.5);">Year</label>
                    <input type="number" min="2000" max="2100" name="year"
                        value="{{ $filters['academicYear'] ?? now()->year }}" @disabled($selectedPeriod !== 'year')
                        class="w-full rounded-xl px-4 py-2 text-sm text-primary1"
                        style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                </div>

                @if ($showProgramFilter)
                    <div>
                        <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider"
                            style="color: rgba(255,255,255,0.5);">Program</label>
                        <select name="program_id" class="w-full rounded-xl px-4 py-2 text-sm"
                            style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">
                            <option value="" style="background: var(--color-primary1);">All programs</option>
                            @foreach ($programOptions as $program)
                                <option value="{{ $program['id'] }}" style="background: var(--color-primary1);"
                                    @selected((string) $selectedProgramId === (string) $program['id'])>
                                    {{ $program['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            <div class="mt-4 flex flex-wrap gap-3">
                <button type="submit"
                    class="px-5 py-2 text-sm font-semibold rounded-xl transition-all duration-200 hover:scale-[1.02]"
                    style="background-color: var(--color-accent1); color: var(--color-primary1);">
                    Apply filters
                </button>
                <a href="{{ route($dashboardRouteName) }}"
                    class="px-5 py-2 text-sm font-semibold rounded-xl transition-all duration-200 text-center"
                    style="background-color: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1);">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto overflow-y-hidden mb-6">
        <div class="flex gap-2 border-b min-w-max" style="border-color: rgba(255,255,255,0.08);">
            @canany(['manage-education-records', 'manage-sports-records'])
                <a href="{{ route('dashboard.beneficiary', $tabQuery) }}"
                    class="tab-item inline-flex items-center gap-2 px-5.5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 {{ $activeSection === 'beneficiary' ? 'text-accent1 border-b-2 border-accent1' : 'border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}"
                    style="font-family: var(--font-body1); color: {{ $activeSection === 'beneficiary' ? 'var(--color-accent1)' : 'rgba(255,255,255,0.6)' }};">
                    Beneficiary Management
                </a>
                <a href="{{ route('dashboard.activity', $tabQuery) }}"
                    class="tab-item inline-flex items-center gap-2 px-5.5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 {{ $activeSection === 'activity' ? 'text-accent1 border-b-2 border-accent1' : 'border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}"
                    style="font-family: var(--font-body1); color: {{ $activeSection === 'activity' ? 'var(--color-accent1)' : 'rgba(255,255,255,0.6)' }};">
                    Activity Management & Attendance
                </a>
            @endcanany
            @can('manage-donors-and-grants')
                <a href="{{ route('dashboard.donor', $tabQuery) }}"
                    class="tab-item inline-flex items-center gap-2 px-5.5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 {{ $activeSection === 'donor' ? 'text-accent1 border-b-2 border-accent1' : 'border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}"
                    style="font-family: var(--font-body1); color: {{ $activeSection === 'donor' ? 'var(--color-accent1)' : 'rgba(255,255,255,0.6)' }};">
                    Donor & Grant Management
                </a>
            @endcan
        </div>
    </div>

    @if (
        ($sectionVisibility['beneficiary'] ?? false) ||
            ($sectionVisibility['activity'] ?? false) ||
            ($sectionVisibility['donor'] ?? false))
        <div class="space-y-8">
            @if (($sectionVisibility['beneficiary'] ?? false) && $activeSection === 'beneficiary')
                <section class="space-y-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-white">Beneficiary Management</h2>
                        <p class="text-white/60 text-sm mt-1">Scholar, player, graduate, transition, completion, and
                            competition metrics.</p>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3 xl:grid-cols-4">
                        @can('manage-education-records')
                            @if ($canShowEducation)
                                <div class="rounded-2xl p-5 relative overflow-hidden group transition-all duration-200 hover:scale-[1.02]"
                                    style="background: linear-gradient(135deg, rgba(59,130,246,0.12) 0%, rgba(59,130,246,0.03) 100%); border: 1px solid rgba(59,130,246,0.15);">
                                    <div class="absolute top-0 right-0 w-24 h-24 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2 transition-all duration-200 group-hover:opacity-20"
                                        style="background: #3b82f6;"></div>
                                    <p class="text-xs uppercase tracking-wider text-white/50">Active Scholars</p>
                                    <p class="mt-2 text-2xl font-bold text-white">
                                        {{ number_format((int) ($beneficiaryMetrics['scholars'] ?? 0)) }}</p>
                                    <p class="mt-2 text-sm text-white/60">Beneficiaries with active scholar status as of
                                        selected period</p>
                                </div>

                                <div class="rounded-2xl p-5 relative overflow-hidden group transition-all duration-200 hover:scale-[1.02]"
                                    style="background: linear-gradient(135deg, rgba(59,130,246,0.12) 0%, rgba(59,130,246,0.03) 100%); border: 1px solid rgba(59,130,246,0.15);">
                                    <div class="absolute top-0 right-0 w-24 h-24 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2 transition-all duration-200 group-hover:opacity-20"
                                        style="background: #3b82f6;"></div>
                                    <p class="text-xs uppercase tracking-wider text-white/50">Total Graduates</p>
                                    <p class="mt-2 text-2xl font-bold text-white">
                                        {{ number_format((int) ($beneficiaryMetrics['graduatesTotal'] ?? 0)) }}</p>
                                    <p class="mt-2 text-sm text-white/60">Calculates from academic year starting in
                                        {{ $dateRangeStart->format('Y') }}</p>
                                </div>
                            @endif
                        @endcan

                        @can('manage-sports-records')
                            @if ($canShowSports)
                                <div class="rounded-2xl p-5 relative overflow-hidden group transition-all duration-200 hover:scale-[1.02]"
                                    style="background: linear-gradient(135deg, rgba(245,158,11,0.12) 0%, rgba(245,158,11,0.03) 100%); border: 1px solid rgba(245,158,11,0.15);">
                                    <div class="absolute top-0 right-0 w-24 h-24 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2 transition-all duration-200 group-hover:opacity-20"
                                        style="background: #f59e0b;"></div>
                                    <p class="text-xs uppercase tracking-wider text-white/50">Registered Players</p>
                                    <p class="mt-2 text-2xl font-bold text-white">
                                        {{ number_format((int) ($beneficiaryMetrics['registeredPlayers'] ?? 0)) }}</p>
                                    <p class="mt-2 text-sm text-white/60">Beneficiaries with active player status as of
                                        selected period</p>
                                </div>
                            @endif
                        @endcan

                        <div class="rounded-2xl p-5 relative overflow-hidden group transition-all duration-200 hover:scale-[1.02]"
                            style="background: linear-gradient(135deg, rgba(168,85,247,0.12) 0%, rgba(168,85,247,0.03) 100%); border: 1px solid rgba(168,85,247,0.15);">
                            <div class="absolute top-0 right-0 w-24 h-24 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2 transition-all duration-200 group-hover:opacity-20"
                                style="background: #a855f7;"></div>
                            <p class="text-xs uppercase tracking-wider text-white/50">Competitions with Participation
                            </p>
                            <p class="mt-2 text-2xl font-bold text-white">
                                {{ number_format((int) ($beneficiaryMetrics['competitionParticipation'] ?? 0)) }}</p>
                            <p class="mt-2 text-sm text-white/60">Total participated competitions as of selected period
                            </p>
                        </div>
                    </div>

                    @can('manage-education-records')
                        @if ($canShowEducation)
                            @php
                                $stageColors = [
                                    'elementary' => 'rgba(255, 204, 51, 0.95)',
                                    'high_school' => 'rgba(59, 130, 246, 0.95)',
                                    'shs' => 'rgba(16, 185, 129, 0.95)',
                                    'college' => 'rgba(234, 88, 12, 0.95)',
                                ];
                                $stageLabels = [
                                    'elementary' => 'Grade Level 1–6',
                                    'high_school' => 'Grade Level 7–10',
                                    'shs' => 'Grade Level 11–12',
                                    'college' => 'Tertiary Education',
                                ];
                            @endphp
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="rounded-2xl p-5 bg-white/5 border border-white/10 only:col-span-2">
                                    <h3 class="text-lg font-semibold text-white mb-2">Average Grades by Educational Stage
                                    </h3>
                                    <p class="text-xs tracking-[0.2em] text-white/40 mb-4">Calculates from academic year
                                        starting in {{ $dateRangeStart->format('Y') }}</p>
                                    <div class="grid gap-4 grid-cols-2">
                                        @foreach ($beneficiaryMetrics['averageGradesByStage'] ?? [] as $stage => $value)
                                            <div class="rounded-2xl p-4 text-center relative overflow-hidden group"
                                                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                                <div class="absolute top-0 left-0 w-full h-20 opacity-20 transition-opacity group-hover:opacity-30"
                                                    style="background: linear-gradient(180deg, {{ $stageColors[$stage] ?? 'var(--color-info)' }} 0%, transparent 100%);">
                                                </div>
                                                <div class="relative w-28 h-28 mx-auto mb-2 z-10">
                                                    <svg class="w-28 h-28 -rotate-90 transform">
                                                        <circle cx="56" cy="56" r="48" fill="none"
                                                            stroke="rgba(255,255,255,0.05)" stroke-width="7" />
                                                        <circle cx="56" cy="56" r="48" fill="none"
                                                            stroke="{{ $stageColors[$stage] ?? 'rgba(255,255,255,0.2)' }}"
                                                            stroke-width="7" stroke-linecap="round"
                                                            stroke-dasharray="301.59"
                                                            stroke-dashoffset="{{ (float) $value > 0 ? 301.59 - (301.59 * (float) $value) / 100 : 301.59 }}" />
                                                    </svg>
                                                    <div
                                                        class="absolute inset-0 flex flex-col items-center justify-center">
                                                        <span class="text-lg font-bold"
                                                            style="color: {{ $stageColors[$stage] ?? 'white' }};">
                                                            {{ number_format((float) $value, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <p class="text-xs uppercase tracking-[0.2em] text-white/40 relative z-10">
                                                    {{ Str::headline($stage) }}</p>
                                                <p class="text-xs text-white/30 mt-0.5 relative z-10">
                                                    {{ $stageLabels[$stage] ?? '' }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                @if (auth()->user()?->staff?->position?->name == 'Social Worker' ||
                                        auth()->user()?->staff?->position?->name == 'Researcher' ||
                                        auth()->user()?->staff?->position?->name == 'System Admin')
                                    <div class="rounded-2xl p-5 bg-white/5 border border-white/10">
                                        <h3 class="text-lg font-semibold text-white mb-2">Average Socio-Emotional Score by
                                            Educational Stage</h3>
                                        <p class="text-xs tracking-[0.2em] text-white/40 mb-4">Calculates from selected
                                            period</p>
                                        <div class="grid gap-4 grid-cols-2">
                                            @foreach ($averageSocioEmotionalByStage as $stage => $value)
                                                <div class="rounded-2xl p-4 text-center relative overflow-hidden group"
                                                    style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                                    <div class="absolute top-0 left-0 w-full h-20 opacity-20 transition-opacity group-hover:opacity-30"
                                                        style="background: linear-gradient(180deg, {{ $stageColors[$stage] ?? 'var(--color-info)' }} 0%, transparent 100%);">
                                                    </div>
                                                    <div class="relative w-28 h-28 mx-auto mb-2 z-10">
                                                        <svg class="w-28 h-28 -rotate-90 transform">
                                                            <circle cx="56" cy="56" r="48" fill="none"
                                                                stroke="rgba(255,255,255,0.05)" stroke-width="7" />
                                                            <circle cx="56" cy="56" r="48" fill="none"
                                                                stroke="{{ $stageColors[$stage] ?? 'rgba(255,255,255,0.2)' }}"
                                                                stroke-width="7" stroke-linecap="round"
                                                                stroke-dasharray="301.59"
                                                                stroke-dashoffset="{{ (float) $value > 0 ? 301.59 - (301.59 * (float) $value) / 100 : 301.59 }}" />
                                                        </svg>
                                                        <div
                                                            class="absolute inset-0 flex flex-col items-center justify-center">
                                                            <span class="text-lg font-bold"
                                                                style="color: {{ $stageColors[$stage] ?? 'white' }};">
                                                                {{ number_format((float) $value, 2) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <p
                                                        class="text-xs uppercase tracking-[0.2em] text-white/40 relative z-10">
                                                        {{ Str::headline($stage) }}</p>
                                                    <p class="text-xs text-white/30 mt-0.5 relative z-10">Socio-Emotional
                                                        Score</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endcan

                    @include('dashboard.partials.charts-beneficiary')

                </section>
            @endif

            @if (($sectionVisibility['activity'] ?? false) && $activeSection === 'activity')
                <section class="space-y-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-white">Activity Management & Attendance Tracking</h2>
                        <p class="text-white/60 text-sm mt-1">Attendance and unique-visit metrics for education and
                            sports activities.</p>
                    </div>
                    <div class="grid gap-4 md:grid-cols-4">
                        @can('manage-education-records')
                            @if ($canShowEducation)
                                <div class="rounded-2xl p-5 relative overflow-hidden group transition-all duration-200 hover:scale-[1.02]"
                                    style="background: linear-gradient(135deg, rgba(59,130,246,0.12) 0%, rgba(59,130,246,0.03) 100%); border: 1px solid rgba(59,130,246,0.15);">
                                    <div class="absolute top-0 right-0 w-24 h-24 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2 transition-all duration-200 group-hover:opacity-20"
                                        style="background: #3b82f6;"></div>
                                    <p class="text-xs uppercase tracking-wider text-white/50">EQ Session Attendance</p>
                                    <p class="mt-2 text-2xl font-bold text-white">
                                        {{ number_format((float) ($activityMetrics['eqSessionAttendance'] ?? 0), 2) }}%</p>
                                </div>
                                <div class="rounded-2xl p-5 relative overflow-hidden group transition-all duration-200 hover:scale-[1.02]"
                                    style="background: linear-gradient(135deg, rgba(59,130,246,0.12) 0%, rgba(59,130,246,0.03) 100%); border: 1px solid rgba(59,130,246,0.15);">
                                    <div class="absolute top-0 right-0 w-24 h-24 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2 transition-all duration-200 group-hover:opacity-20"
                                        style="background: #3b82f6;"></div>
                                    <p class="text-xs uppercase tracking-wider text-white/50">Tutorial Session Attendance
                                    </p>
                                    <p class="mt-2 text-2xl font-bold text-white">
                                        {{ number_format((float) ($activityMetrics['tutorialSessionAttendance'] ?? 0), 2) }}%
                                    </p>
                                </div>
                            @endif
                        @endcan

                        @can('manage-sports-records')
                            @if ($canShowSports)
                                <div class="rounded-2xl p-5 relative overflow-hidden group transition-all duration-200 hover:scale-[1.02]"
                                    style="background: linear-gradient(135deg, rgba(245,158,11,0.12) 0%, rgba(245,158,11,0.03) 100%); border: 1px solid rgba(245,158,11,0.15);">
                                    <div class="absolute top-0 right-0 w-24 h-24 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2 transition-all duration-200 group-hover:opacity-20"
                                        style="background: #f59e0b;"></div>
                                    <p class="text-xs uppercase tracking-wider text-white/50">Training Session Attendance
                                    </p>
                                    <p class="mt-2 text-2xl font-bold text-white">
                                        {{ number_format((float) ($activityMetrics['trainingSessionAttendance'] ?? 0), 2) }}%
                                    </p>
                                </div>
                                <div class="rounded-2xl p-5 relative overflow-hidden group transition-all duration-200 hover:scale-[1.02]"
                                    style="background: linear-gradient(135deg, rgba(245,158,11,0.12) 0%, rgba(245,158,11,0.03) 100%); border: 1px solid rgba(245,158,11,0.15);">
                                    <div class="absolute top-0 right-0 w-24 h-24 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2 transition-all duration-200 group-hover:opacity-20"
                                        style="background: #f59e0b;"></div>
                                    <p class="text-xs uppercase tracking-wider text-white/50">Unique Visits</p>
                                    <p class="mt-2 text-2xl font-bold text-white">
                                        {{ number_format((int) ($activityMetrics['uniqueVisits'] ?? 0)) }}</p>
                                </div>
                            @endif
                        @endcan

                    </div>

                    <div class="grid gap-4 md:grid-cols-1">
                        <div class="rounded-2xl p-5 bg-white/5 border border-white/10 relative overflow-hidden group">
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-white">Attendance Trend</h3>
                                    <span class="text-xs uppercase tracking-wider text-white/40">EQ, tutorial, and
                                        training</span>
                                </div>
                                <div class="h-56">
                                    <canvas data-dashboard-chart
                                        data-chart-config='@json($attendanceTrendConfig)'></canvas>
                                </div>
                            </div>
                        </div>

                        @can('manage-sports-records')
                            @if ($canShowSports)
                                <div
                                    class="rounded-2xl p-5 bg-white/5 border border-white/10 relative overflow-hidden group">
                                    <div class="relative z-10">
                                        <div class="flex items-center justify-between mb-4">
                                            <h3 class="text-lg font-semibold text-white">Unique Visits Trend</h3>
                                            <span class="text-xs uppercase tracking-wider text-white/40">Visits per
                                                period</span>
                                        </div>
                                        <div class="h-56">
                                            <canvas data-dashboard-chart
                                                data-chart-config='@json($uniqueVisitsTrendConfig)'></canvas>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endcan

                    </div>
                </section>
            @endif

            @if (($sectionVisibility['donor'] ?? false) && $activeSection === 'donor')
                <section class="space-y-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-white">Donor & Grant Management</h2>
                        <p class="text-white/60 text-sm mt-1">Funding and deliverables overview for the selected date
                            range.</p>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 mb-4">
                        <div class="rounded-2xl p-5 relative overflow-hidden group transition-all duration-200 hover:scale-[1.02]"
                            style="background: linear-gradient(135deg, rgba(16,185,129,0.12) 0%, rgba(16,185,129,0.03) 100%); border: 1px solid rgba(16,185,129,0.15);">
                            <div class="absolute top-0 right-0 w-24 h-24 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2 transition-all duration-200 group-hover:opacity-20"
                                style="background: #10b981;"></div>
                            <p class="text-xs uppercase tracking-wider text-white/50">New Donors</p>
                            <p class="mt-2 text-2xl font-bold text-white">
                                {{ number_format((int) ($donorMetrics['newDonors'] ?? 0)) }}</p>
                        </div>
                        <div class="rounded-2xl p-5 relative overflow-hidden group transition-all duration-200 hover:scale-[1.02]"
                            style="background: linear-gradient(135deg, rgba(99,102,241,0.12) 0%, rgba(99,102,241,0.03) 100%); border: 1px solid rgba(99,102,241,0.15);">
                            <div class="absolute top-0 right-0 w-24 h-24 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2 transition-all duration-200 group-hover:opacity-20"
                                style="background: #6366f1;"></div>
                            <p class="text-xs uppercase tracking-wider text-white/50">Grants Received</p>
                            <p class="mt-2 text-2xl font-bold text-white">
                                {{ number_format((int) ($donorMetrics['grantsReceived'] ?? 0)) }}</p>
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="rounded-2xl p-5 relative overflow-hidden group transition-all duration-200 hover:scale-[1.02]"
                            style="background: linear-gradient(135deg, rgba(59,130,246,0.12) 0%, rgba(59,130,246,0.03) 100%); border: 1px solid rgba(59,130,246,0.15);">
                            <div class="absolute top-0 right-0 w-24 h-24 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2 transition-all duration-200 group-hover:opacity-20"
                                style="background: #3b82f6;"></div>
                            <p class="text-xs uppercase tracking-wider text-white/50">Funds Received</p>
                            <p class="mt-2 text-2xl font-bold text-white">
                                {{ number_format((float) ($donorMetrics['fundsReceived'] ?? 0), 2) }}</p>
                        </div>
                        <div class="rounded-2xl p-5 relative overflow-hidden group transition-all duration-200 hover:scale-[1.02]"
                            style="background: linear-gradient(135deg, rgba(245,158,11,0.12) 0%, rgba(245,158,11,0.03) 100%); border: 1px solid rgba(245,158,11,0.15);">
                            <div class="absolute top-0 right-0 w-24 h-24 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2 transition-all duration-200 group-hover:opacity-20"
                                style="background: #f59e0b;"></div>
                            <p class="text-xs uppercase tracking-wider text-white/50">Funds Allocated</p>
                            <p class="mt-2 text-2xl font-bold text-white">
                                {{ number_format((float) ($donorMetrics['fundsAllocated'] ?? 0), 2) }}</p>
                        </div>
                        <div class="rounded-2xl p-5 relative overflow-hidden group transition-all duration-200 hover:scale-[1.02]"
                            style="background: linear-gradient(135deg, rgba(168,85,247,0.12) 0%, rgba(168,85,247,0.03) 100%); border: 1px solid rgba(168,85,247,0.15);">
                            <div class="absolute top-0 right-0 w-24 h-24 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2 transition-all duration-200 group-hover:opacity-20"
                                style="background: #a855f7;"></div>
                            <p class="text-xs uppercase tracking-wider text-white/50">Funding Target</p>
                            <p class="mt-2 text-2xl font-bold text-white">
                                {{ number_format((float) ($donorMetrics['fundingTarget'] ?? 0), 2) }}</p>
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="rounded-2xl p-6 group transition-all duration-200 hover:scale-[1.02]"
                            style="background-color: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                            <div class="flex items-center justify-between gap-4 mb-4">
                                <div>
                                    <h2 class="text-xl font-bold text-white"
                                        style="font-family: var(--font-header1);">Funds Received vs Target</h2>
                                    <p class="text-white/60 text-sm">Progress toward this year's fundraising target</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-white/50 uppercase tracking-wider">Completion</div>
                                    <div class="text-2xl font-bold text-white">
                                        {{ number_format((float) ($donorMetrics['receivedProgressPercent'] ?? 0), 1) }}%
                                    </div>
                                </div>
                            </div>
                            <div class="w-full h-4 rounded-full overflow-hidden"
                                style="background-color: rgba(255,255,255,0.08);">
                                <div class="h-full rounded-full"
                                    style="width: {{ (float) ($donorMetrics['receivedProgressPercent'] ?? 0) }}%; background-color: var(--color-accent1);">
                                </div>
                            </div>
                            <div class="mt-4 text-sm text-white/70">
                                {{ number_format((float) ($donorMetrics['receivedProgressPercent'] ?? 0), 1) }}% of the
                                target has been received.
                            </div>
                        </div>

                        <div class="rounded-2xl p-6 group transition-all duration-200 hover:scale-[1.02]"
                            style="background-color: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                            <div class="flex items-center justify-between gap-4 mb-4">
                                <div>
                                    <h2 class="text-xl font-bold text-white"
                                        style="font-family: var(--font-header1);">Funds Allocated vs Received</h2>
                                    <p class="text-white/60 text-sm">How much of received funds have been assigned</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-white/50 uppercase tracking-wider">Allocation Rate</div>
                                    <div class="text-2xl font-bold text-white">
                                        {{ number_format((float) ($donorMetrics['allocatedVsReceivedPercent'] ?? 0), 1) }}%
                                    </div>
                                </div>
                            </div>
                            <div class="w-full h-4 rounded-full overflow-hidden"
                                style="background-color: rgba(255,255,255,0.08);">
                                <div class="h-full rounded-full"
                                    style="width: {{ (float) ($donorMetrics['allocatedVsReceivedPercent'] ?? 0) }}%; background-color: var(--color-success);">
                                </div>
                            </div>
                            <div class="mt-4 text-sm text-white/70">
                                {{ number_format((float) ($donorMetrics['allocatedVsReceivedPercent'] ?? 0), 1) }}% of
                                funds received this year have been allocated.
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl p-5 bg-white/5 border border-white/10 relative overflow-hidden group">
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-white">Upcoming Deliverables</h3>
                                <span class="text-xs uppercase tracking-wider text-white/40">Nearest deadlines</span>
                            </div>
                            <div class="text-white/70 text-sm mb-4">
                                {{ number_format((int) ($donorMetrics['upcomingDeliverables'] ?? 0)) }} deliverables
                                due
                                soon.
                            </div>

                            <div class="deliverables-gantt-shell rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div id="deliverables-gantt-chart" class="min-w-240" data-update-url-base=""></div>
                                <script type="application/json" id="deliverables-gantt-tasks">@json($deliverablesGanttTasks)</script>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl p-6 group transition-all duration-200 hover:scale-[1.02]"
                        style="background-color: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                        <div class="flex items-center justify-between gap-4 mb-5">
                            <div>
                                <h2 class="text-xl font-bold text-white" style="font-family: var(--font-header1);">
                                    Funds Received vs Allocated Comparison by {{ $trendPeriodLabel }}</h2>
                                <p class="text-white/60 text-sm">Received vs allocated funds by {{ strtolower($trendPeriodLabel) }}</p>
                            </div>
                        </div>
                        <div class="h-72">
                            <canvas data-dashboard-chart data-chart-config='@json($fundsTrendConfig)'></canvas>
                        </div>
                    </div>
                </section>
            @endif
        </div>
    @else
        <div class="rounded-2xl p-6 bg-white/5 border border-white/10 text-white/70">
            No dashboard sections are available for the current account.
        </div>
    @endif
</x-dashboardlayout>
