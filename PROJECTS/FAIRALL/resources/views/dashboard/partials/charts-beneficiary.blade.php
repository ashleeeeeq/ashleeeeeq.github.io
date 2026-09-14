@php
        $stageColors = [
            'rgba(255, 204, 51, 0.9)',
            'rgba(59, 130, 246, 0.9)',
            'rgba(16, 185, 129, 0.9)',
            'rgba(234, 88, 12, 0.9)',
        ];
        $stageFillColors = [
            'rgba(255, 204, 51, 0.18)',
            'rgba(59, 130, 246, 0.14)',
            'rgba(16, 185, 129, 0.14)',
            'rgba(234, 88, 12, 0.14)',
        ];
        $stageLabels = [
            'elementary' => 'Elementary',
            'high_school' => 'High School',
            'shs' => 'SHS',
            'college' => 'College',
        ];
        $transitionLabels = [
            'elementary' => 'Elementary to High School',
            'high_school' => 'High School to SHS',
            'shs' => 'SHS to College',
        ];

        $transitionRates = array_filter(
            $beneficiaryMetrics['transitionRates'] ?? [],
            fn($v) => $v > 0
        );
        $transitionRateBreakdown = $beneficiaryMetrics['transitionRateBreakdown'] ?? [];
        $transitionRatesByAcademicYear = $beneficiaryMetrics['transitionRatesByAcademicYear'] ?? [];
        $transitionYearLabels = $transitionRatesByAcademicYear['labels'] ?? [];
        $useTransitionSeries = count($transitionYearLabels) > 1 && $dateRangeStart->year !== $dateRangeEnd->year;

        if ($useTransitionSeries) {
            $transitionRateConfig = [
                'type' => 'line',
                'dashboardMetric' => 'transitionRate',
                'data' => [
                    'labels' => $transitionYearLabels,
                    'datasets' => array_map(
                        function ($dataset, $index) use ($stageColors, $stageFillColors) {
                            return [
                                'label' => $dataset['label'] ?? "Series {$index}",
                                'data' => $dataset['data'] ?? [],
                                'borderColor' => $stageColors[$index % count($stageColors)],
                                'backgroundColor' => $stageFillColors[$index % count($stageFillColors)],
                                'tooltipCounts' => array_map(
                                    fn($success, $total) => [
                                        'success' => (int) $success,
                                        'total' => (int) $total,
                                    ],
                                    $dataset['successCounts'] ?? [],
                                    $dataset['totalCounts'] ?? [],
                                ),
                                'tension' => 0.25,
                            ];
                        },
                        $transitionRatesByAcademicYear['datasets'] ?? [],
                        array_keys($transitionRatesByAcademicYear['datasets'] ?? []),
                    ),
                ],
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'plugins' => ['legend' => ['position' => 'bottom']],
                    'scales' => [
                        'x' => ['title' => ['display' => true, 'text' => 'Year']],
                        'y' => [
                            'beginAtZero' => true,
                            'suggestedMax' => 100,
                            'title' => ['display' => true, 'text' => '% Transitioned'],
                        ],
                    ],
                ],
            ];
            $transitionRateConfig['data']['datasets'] = array_values(array_filter(
                $transitionRateConfig['data']['datasets'],
                fn($ds) => !empty(array_filter($ds['data'] ?? [], fn($v) => $v > 0))
            ));
            if (empty($transitionRateConfig['data']['datasets'])) {
                $transitionRateConfig = null;
            }
        } else {
            $transitionOrder = array_keys($transitionRates);
            $transitionRateConfig = [
                'type' => 'bar',
                'dashboardMetric' => 'transitionRate',
                'data' => [
                    'labels' => array_map(
                        fn($stage) => $transitionLabels[$stage] ?? \Illuminate\Support\Str::headline($stage),
                        $transitionOrder,
                    ),
                    'datasets' => [
                        [
                            'label' => 'Transition Rate',
                            'data' => array_values($transitionRates),
                            'backgroundColor' => array_slice($stageColors, 0, max(1, count($transitionRates))),
                            'tooltipCounts' => array_map(
                                fn($stage) => [
                                    'success' => (int) ($transitionRateBreakdown['successCounts'][$stage] ?? 0),
                                    'total' => (int) ($transitionRateBreakdown['totalCounts'][$stage] ?? 0),
                                ],
                                $transitionOrder,
                            ),
                            'borderRadius' => 12,
                        ],
                    ],
                ],
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'plugins' => ['legend' => ['display' => false]],
                    'scales' => [
                        'x' => ['title' => ['display' => true, 'text' => 'Educational Stage']],
                        'y' => [
                            'beginAtZero' => true,
                            'suggestedMax' => 100,
                            'title' => ['display' => true, 'text' => '% Transitioned'],
                        ],
                    ],
                ],
            ];
        }

        if (!$useTransitionSeries && empty($transitionRates)) {
            $transitionRateConfig = null;
        }

        $completionRates = array_filter(
            $beneficiaryMetrics['completionRates'] ?? [],
            fn($v) => $v > 0
        );
        $completionRateBreakdown = $beneficiaryMetrics['completionRateBreakdown'] ?? [];
        $completionRatesByAcademicYear = $beneficiaryMetrics['completionRatesByAcademicYear'] ?? [];
        $completionYearLabels = $completionRatesByAcademicYear['labels'] ?? [];
        $useCompletionSeries = count($completionYearLabels) > 1 && $dateRangeStart->year !== $dateRangeEnd->year;

        if ($useCompletionSeries) {
            $completionRateConfig = [
                'type' => 'line',
                'dashboardMetric' => 'completionRate',
                'data' => [
                    'labels' => $completionYearLabels,
                    'datasets' => array_map(
                        function ($dataset, $index) use ($stageColors, $stageFillColors) {
                            return [
                                'label' => $dataset['label'] ?? "Series {$index}",
                                'data' => $dataset['data'] ?? [],
                                'borderColor' => $stageColors[$index % count($stageColors)],
                                'backgroundColor' => $stageFillColors[$index % count($stageFillColors)],
                                'tooltipCounts' => array_map(
                                    fn($completed, $total) => [
                                        'completed' => (int) $completed,
                                        'total' => (int) $total,
                                    ],
                                    $dataset['completedCounts'] ?? [],
                                    $dataset['totalCounts'] ?? [],
                                ),
                                'tension' => 0.25,
                            ];
                        },
                        $completionRatesByAcademicYear['datasets'] ?? [],
                        array_keys($completionRatesByAcademicYear['datasets'] ?? []),
                    ),
                ],
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'plugins' => ['legend' => ['position' => 'bottom']],
                    'scales' => [
                        'x' => ['title' => ['display' => true, 'text' => 'Year']],
                        'y' => [
                            'beginAtZero' => true,
                            'suggestedMax' => 100,
                            'title' => ['display' => true, 'text' => '% Completed'],
                        ],
                    ],
                ],
            ];
            $completionRateConfig['data']['datasets'] = array_values(array_filter(
                $completionRateConfig['data']['datasets'],
                fn($ds) => !empty(array_filter($ds['data'] ?? [], fn($v) => $v > 0))
            ));
            if (empty($completionRateConfig['data']['datasets'])) {
                $completionRateConfig = null;
            }
        } else {
            $completionOrder = array_keys($completionRates);
            $completionRateConfig = [
                'type' => 'bar',
                'dashboardMetric' => 'completionRate',
                'data' => [
                    'labels' => array_map(
                        fn($stage) => $stageLabels[$stage] ?? \Illuminate\Support\Str::headline($stage),
                        $completionOrder,
                    ),
                    'datasets' => [
                        [
                            'label' => 'Completion Rate',
                            'data' => array_values($completionRates),
                            'backgroundColor' => array_slice($stageColors, 0, max(1, count($completionRates))),
                            'tooltipCounts' => array_map(
                                fn($stage) => [
                                    'completed' => (int) ($completionRateBreakdown['completedCounts'][$stage] ?? 0),
                                    'total' => (int) ($completionRateBreakdown['totalCounts'][$stage] ?? 0),
                                ],
                                $completionOrder,
                            ),
                            'borderRadius' => 12,
                        ],
                    ],
                ],
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'plugins' => ['legend' => ['display' => false]],
                    'scales' => [
                        'x' => ['title' => ['display' => true, 'text' => 'Educational Stage']],
                        'y' => [
                            'beginAtZero' => true,
                            'suggestedMax' => 100,
                            'title' => ['display' => true, 'text' => '% Completed'],
                        ],
                    ],
                ],
            ];
        }

        if (!$useCompletionSeries && empty($completionRates)) {
            $completionRateConfig = null;
        }

        $graduatesByStage = array_filter(
            $beneficiaryMetrics['graduatesByStage'] ?? [],
            fn($v) => $v > 0
        );
        $graduatesByStageByYear = $beneficiaryMetrics['graduatesByStageByYear'] ?? ['labels' => [], 'datasets' => []];
        $useGraduateYearSeries = $dateRangeStart->year !== $dateRangeEnd->year && count($graduatesByStageByYear['labels'] ?? []) > 1;
        $graduateStageColors = [
            'rgba(255, 204, 51, 0.88)',
            'rgba(59, 130, 246, 0.88)',
            'rgba(16, 185, 129, 0.88)',
            'rgba(234, 88, 12, 0.88)',
        ];
        $graduateStageLineColors = [
            'rgba(255, 204, 51, 0.95)',
            'rgba(59, 130, 246, 0.95)',
            'rgba(16, 185, 129, 0.95)',
            'rgba(234, 88, 12, 0.95)',
        ];

        if ($useGraduateYearSeries) {
            $graduatesByStageChartConfig = [
                'type' => 'line',
                'data' => [
                    'labels' => $graduatesByStageByYear['labels'] ?? [],
                    'datasets' => array_map(
                        function ($dataset, $index) use ($graduateStageLineColors) {
                            return [
                                'label' => $dataset['label'] ?? "Series {$index}",
                                'data' => $dataset['data'] ?? [],
                                'borderColor' => $graduateStageLineColors[$index % count($graduateStageLineColors)],
                                'backgroundColor' => $graduateStageLineColors[$index % count($graduateStageLineColors)],
                                'tension' => 0.25,
                                'fill' => false,
                            ];
                        },
                        $graduatesByStageByYear['datasets'] ?? [],
                        array_keys($graduatesByStageByYear['datasets'] ?? [])
                    ),
                ],
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'plugins' => ['legend' => ['position' => 'bottom']],
                    'scales' => [
                        'x' => ['title' => ['display' => true, 'text' => 'Year']],
                        'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
                    ],
                ],
            ];
            $graduatesByStageChartConfig['data']['datasets'] = array_values(array_filter(
                $graduatesByStageChartConfig['data']['datasets'],
                fn($ds) => !empty(array_filter($ds['data'] ?? [], fn($v) => $v > 0))
            ));
            if (empty($graduatesByStageChartConfig['data']['datasets'])) {
                $graduatesByStageChartConfig = null;
            }
        } elseif (!empty($graduatesByStage)) {
            $graduatesByStageChartConfig = [
                'type' => 'bar',
                'data' => [
                    'labels' => array_map(
                        fn($stage) => \Illuminate\Support\Str::headline($stage),
                        array_keys($graduatesByStage),
                    ),
                    'datasets' => [
                        [
                            'label' => 'Graduates',
                            'data' => array_values($graduatesByStage),
                            'backgroundColor' => array_map(
                                fn($i) => $graduateStageColors[$i % count($graduateStageColors)],
                                range(0, count($graduatesByStage) - 1)
                            ),
                            'borderRadius' => 12,
                        ],
                    ],
                ],
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'plugins' => ['legend' => ['display' => false]],
                    'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
                ],
            ];
        } else {
            $graduatesByStageChartConfig = null;
        }

        $scholarStageCounts = $beneficiaryMetrics['scholarsByEducationalStage'] ?? ['labels' => [], 'data' => []];
        $scholarStageCountsByYear = $beneficiaryMetrics['scholarsByEducationalStageByYear'] ?? [
            'labels' => [],
            'datasets' => [],
        ];
        $playerSportCounts = $beneficiaryMetrics['newPlayersBySportsType'] ?? ['labels' => [], 'data' => []];
        $playerSportCountsByYear = $beneficiaryMetrics['newPlayersBySportsTypeByYear'] ?? [
            'labels' => [],
            'datasets' => [],
        ];
        $useScholarStageYearSeries =
            $dateRangeStart->year !== $dateRangeEnd->year && count($scholarStageCountsByYear['labels'] ?? []) > 1;
        $usePlayerSportYearSeries =
            $dateRangeStart->year !== $dateRangeEnd->year && count($playerSportCountsByYear['labels'] ?? []) > 1;
        $scholarStageColors = [
            'rgba(255, 204, 51, 0.88)',
            'rgba(59, 130, 246, 0.88)',
            'rgba(16, 185, 129, 0.88)',
            'rgba(234, 88, 12, 0.88)',
        ];
        $scholarStageLineColors = [
            'rgba(255, 204, 51, 0.95)',
            'rgba(59, 130, 246, 0.95)',
            'rgba(16, 185, 129, 0.95)',
            'rgba(234, 88, 12, 0.95)',
        ];
        $sportColors = [
            'rgba(244, 114, 182, 0.88)',
            'rgba(14, 165, 233, 0.88)',
            'rgba(34, 197, 94, 0.88)',
            'rgba(245, 158, 11, 0.88)',
            'rgba(168, 85, 247, 0.88)',
            'rgba(239, 68, 68, 0.88)',
            'rgba(20, 184, 166, 0.88)',
            'rgba(99, 102, 241, 0.88)',
        ];
        $sportLineColors = [
            'rgba(244, 114, 182, 0.95)',
            'rgba(14, 165, 233, 0.95)',
            'rgba(34, 197, 94, 0.95)',
            'rgba(245, 158, 11, 0.95)',
            'rgba(168, 85, 247, 0.95)',
            'rgba(239, 68, 68, 0.95)',
            'rgba(20, 184, 166, 0.95)',
            'rgba(99, 102, 241, 0.95)',
        ];

        if ($useScholarStageYearSeries) {
            $scholarStageChartConfig = [
                'type' => 'line',
                'data' => [
                    'labels' => $scholarStageCountsByYear['labels'] ?? [],
                    'datasets' => array_map(
                        function ($dataset, $index) use ($scholarStageLineColors) {
                            return [
                                'label' => $dataset['label'] ?? "Series {$index}",
                                'data' => $dataset['data'] ?? [],
                                'borderColor' => $scholarStageLineColors[$index % count($scholarStageLineColors)],
                                'backgroundColor' => $scholarStageLineColors[$index % count($scholarStageLineColors)],
                                'tension' => 0.25,
                                'fill' => false,
                            ];
                        },
                        $scholarStageCountsByYear['datasets'] ?? [],
                        array_keys($scholarStageCountsByYear['datasets'] ?? []),
                    ),
                ],
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'plugins' => ['legend' => ['position' => 'bottom']],
                    'scales' => [
                        'x' => ['title' => ['display' => true, 'text' => 'Year']],
                        'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
                    ],
                ],
            ];
            $scholarStageChartConfig['data']['datasets'] = array_values(array_filter(
                $scholarStageChartConfig['data']['datasets'],
                fn($ds) => !empty(array_filter($ds['data'] ?? [], fn($v) => $v > 0))
            ));
            if (empty($scholarStageChartConfig['data']['datasets'])) {
                $scholarStageChartConfig = null;
            }
        } else {
            $scholarNonZero = array_filter(
                array_map(null, $scholarStageCounts['labels'] ?? [], $scholarStageCounts['data'] ?? []),
                fn($p) => ($p[1] ?? 0) > 0
            );
            $scholarStageCounts['labels'] = array_values(array_column($scholarNonZero, 0));
            $scholarStageCounts['data'] = array_values(array_column($scholarNonZero, 1));
            $scholarStageLabels = $scholarStageCounts['labels'] ?? [];
            $scholarStageChartConfig = [
                'type' => 'bar',
                'data' => [
                    'labels' => $scholarStageLabels,
                    'datasets' => [
                        [
                            'label' => 'Scholars',
                            'data' => $scholarStageCounts['data'] ?? [],
                            'backgroundColor' => array_map(
                                fn($index) => $scholarStageColors[$index % count($scholarStageColors)],
                                array_keys($scholarStageLabels),
                            ),
                            'borderRadius' => 12,
                        ],
                    ],
                ],
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'plugins' => ['legend' => ['display' => false]],
                    'scales' => [
                        'x' => ['title' => ['display' => true, 'text' => 'Educational Stage']],
                        'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
                    ],
                ],
            ];
        }

        if (!$useScholarStageYearSeries && empty($scholarStageCounts['data'])) {
            $scholarStageChartConfig = null;
        }

        if ($usePlayerSportYearSeries) {
            $playerSportChartConfig = [
                'type' => 'line',
                'data' => [
                    'labels' => $playerSportCountsByYear['labels'] ?? [],
                    'datasets' => array_map(
                        function ($dataset, $index) use ($sportLineColors) {
                            return [
                                'label' => $dataset['label'] ?? "Series {$index}",
                                'data' => $dataset['data'] ?? [],
                                'borderColor' => $sportLineColors[$index % count($sportLineColors)],
                                'backgroundColor' => $sportLineColors[$index % count($sportLineColors)],
                                'tension' => 0.25,
                                'fill' => false,
                            ];
                        },
                        $playerSportCountsByYear['datasets'] ?? [],
                        array_keys($playerSportCountsByYear['datasets'] ?? []),
                    ),
                ],
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'plugins' => ['legend' => ['position' => 'bottom']],
                    'scales' => [
                        'x' => ['title' => ['display' => true, 'text' => 'Year']],
                        'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
                    ],
                ],
            ];
            $playerSportChartConfig['data']['datasets'] = array_values(array_filter(
                $playerSportChartConfig['data']['datasets'],
                fn($ds) => !empty(array_filter($ds['data'] ?? [], fn($v) => $v > 0))
            ));
            if (empty($playerSportChartConfig['data']['datasets'])) {
                $playerSportChartConfig = null;
            }
        } else {
            $playerSportNonZero = array_filter(
                array_map(null, $playerSportCounts['labels'] ?? [], $playerSportCounts['data'] ?? []),
                fn($p) => ($p[1] ?? 0) > 0
            );
            $playerSportCounts['labels'] = array_values(array_column($playerSportNonZero, 0));
            $playerSportCounts['data'] = array_values(array_column($playerSportNonZero, 1));
            $playerSportLabels = $playerSportCounts['labels'] ?? [];
            $playerSportChartConfig = [
                'type' => 'bar',
                'data' => [
                    'labels' => $playerSportLabels,
                    'datasets' => [
                        [
                            'label' => 'Players',
                            'data' => $playerSportCounts['data'] ?? [],
                            'backgroundColor' => array_map(
                                fn($index) => $sportColors[$index % count($sportColors)],
                                array_keys($playerSportLabels),
                            ),
                            'borderRadius' => 12,
                        ],
                    ],
                ],
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'plugins' => ['legend' => ['display' => false]],
                    'scales' => [
                        'x' => ['title' => ['display' => true, 'text' => 'Sports Type']],
                        'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
                    ],
                ],
            ];
        }

        if (!$usePlayerSportYearSeries && empty($playerSportCounts['data'])) {
            $playerSportChartConfig = null;
        }

        $registeredPlayerSportCounts = $beneficiaryMetrics['activePlayersBySportsType'] ?? ['labels' => [], 'data' => []];
        $registeredPlayerSportCountsByYear = $beneficiaryMetrics['activePlayersBySportsTypeByYear'] ?? ['labels' => [], 'datasets' => []];
        $useRegisteredPlayerYearSeries = $dateRangeStart->year !== $dateRangeEnd->year && count($registeredPlayerSportCountsByYear['labels'] ?? []) > 1;

        if ($useRegisteredPlayerYearSeries) {
            $registeredPlayerSportChartConfig = [
                'type' => 'line',
                'data' => [
                    'labels' => $registeredPlayerSportCountsByYear['labels'] ?? [],
                    'datasets' => array_map(
                        function ($dataset, $index) use ($sportLineColors) {
                            return [
                                'label' => $dataset['label'] ?? "Series {$index}",
                                'data' => $dataset['data'] ?? [],
                                'borderColor' => $sportLineColors[$index % count($sportLineColors)],
                                'backgroundColor' => $sportLineColors[$index % count($sportLineColors)],
                                'tension' => 0.25,
                                'fill' => false,
                            ];
                        },
                        $registeredPlayerSportCountsByYear['datasets'] ?? [],
                        array_keys($registeredPlayerSportCountsByYear['datasets'] ?? []),
                    ),
                ],
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'plugins' => ['legend' => ['position' => 'bottom']],
                    'scales' => [
                        'x' => ['title' => ['display' => true, 'text' => 'Year']],
                        'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
                    ],
                ],
            ];
            $registeredPlayerSportChartConfig['data']['datasets'] = array_values(array_filter(
                $registeredPlayerSportChartConfig['data']['datasets'],
                fn($ds) => !empty(array_filter($ds['data'] ?? [], fn($v) => $v > 0))
            ));
            if (empty($registeredPlayerSportChartConfig['data']['datasets'])) {
                $registeredPlayerSportChartConfig = null;
            }
        } else {
            $registeredPlayerSportNonZero = array_filter(
                array_map(null, $registeredPlayerSportCounts['labels'] ?? [], $registeredPlayerSportCounts['data'] ?? []),
                fn($p) => ($p[1] ?? 0) > 0
            );
            $registeredPlayerSportCounts['labels'] = array_values(array_column($registeredPlayerSportNonZero, 0));
            $registeredPlayerSportCounts['data'] = array_values(array_column($registeredPlayerSportNonZero, 1));
            $registeredPlayerSportChartConfig = [
                'type' => 'bar',
                'data' => [
                    'labels' => $registeredPlayerSportCounts['labels'] ?? [],
                    'datasets' => [
                        [
                            'label' => 'Registered Players',
                            'data' => $registeredPlayerSportCounts['data'] ?? [],
                            'backgroundColor' => array_map(
                                fn($index) => $sportColors[$index % count($sportColors)],
                                array_keys($registeredPlayerSportCounts['labels'] ?? []),
                            ),
                            'borderRadius' => 12,
                        ],
                    ],
                ],
                'options' => [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'plugins' => ['legend' => ['display' => false]],
                    'scales' => [
                        'x' => ['title' => ['display' => true, 'text' => 'Sports Type']],
                        'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
                    ],
                ],
            ];
        }

        if (!$useRegisteredPlayerYearSeries && empty($registeredPlayerSportCounts['data'])) {
            $registeredPlayerSportChartConfig = null;
        }
@endphp

@include('dashboard.partials.beneficiary-charts-body')
