                    @can('manage-education-records')
                        @if ($canShowEducation)
                            <div class="grid gap-4 xl:grid-cols-2 mt-4">
                                @if($transitionRateConfig)
                                <div class="rounded-2xl p-5 bg-white/5 border border-white/10 relative overflow-hidden group only:col-span-2">
                                    <div class="relative z-10">
                                        <div class="flex items-center justify-between mb-2">
                                            <h3 class="text-lg font-semibold text-white">Transition Rate</h3>
                                            <span class="text-xs uppercase tracking-wider text-white/40">
                                                {{ count($transitionYearLabels) > 1 ? 'Time Series' : 'Stage comparison' }}
                                            </span>
                                        </div>
                                        <p class="text-xs tracking-[0.2em] text-white/40 mb-2">Calculates from academic year starting in {{ $dateRangeStart->format('Y') }}</p>
                                        <div class="h-64">
                                            <canvas data-dashboard-chart
                                                data-chart-config='@json($transitionRateConfig)'></canvas>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($completionRateConfig)
                                <div class="rounded-2xl p-5 bg-white/5 border border-white/10 relative overflow-hidden group only:col-span-2">
                                    <div class="relative z-10">
                                        <div class="flex items-center justify-between mb-2">
                                            <h3 class="text-lg font-semibold text-white">Completion Rate</h3>
                                            <span class="text-xs uppercase tracking-wider text-white/40">
                                                {{ count($completionYearLabels) > 1 ? 'Time Series' : 'Stage comparison' }}
                                            </span>
                                        </div>
                                        <p class="text-xs tracking-[0.2em] text-white/40 mb-2">Calculates from academic year starting in {{ $dateRangeStart->format('Y') }}</p>
                                        <div class="h-64">
                                            <canvas data-dashboard-chart
                                                data-chart-config='@json($completionRateConfig)'></canvas>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                @if($graduatesByStageChartConfig)
                                <div class="rounded-2xl p-5 bg-white/5 border border-white/10 relative overflow-hidden group only:col-span-2">
                                    <div class="relative z-10">
                                        <div class="flex items-center justify-between mb-2">
                                            <h3 class="text-lg font-semibold text-white">Number of Graduates by Educational
                                                Stage
                                            </h3>
                                            <span class="text-xs uppercase tracking-wider text-white/40">
                                                {{ $useScholarStageYearSeries ? 'Time Series' : 'Stage comparison' }}
                                            </span>
                                        </div>
                                        <p class="text-xs tracking-[0.2em] text-white/40 mb-2">Calculates from academic year starting in {{ $dateRangeStart->format('Y') }}</p>
                                        <div class="h-64">
                                            <canvas data-dashboard-chart
                                                data-chart-config='@json($graduatesByStageChartConfig)'></canvas>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($scholarStageChartConfig)
                                <div class="rounded-2xl p-5 bg-white/5 border border-white/10 relative overflow-hidden group only:col-span-2">
                                    <div class="relative z-10">
                                        <div class="flex items-center justify-between mb-2">
                                            <h3 class="text-lg font-semibold text-white">Scholars by Educational Stage</h3>
                                            <span class="text-xs uppercase tracking-wider text-white/40 mb-2">
                                                {{ $useScholarStageYearSeries ? 'Time Series' : 'Stage comparison' }}
                                            </span>
                                        </div>
                                        <p class="text-xs tracking-[0.2em] text-white/40 mb-2">Calculates from academic year starting in {{ $dateRangeStart->format('Y') }}</p>
                                        <div class="h-64">
                                            <canvas data-dashboard-chart
                                                data-chart-config='@json($scholarStageChartConfig)'></canvas>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        @endif
                    @endcan

                    @can('manage-sports-records')
                        @if ($canShowSports)
                        @if($playerSportChartConfig)
                            <div class="rounded-2xl p-5 bg-white/5 border border-white/10 relative overflow-hidden group">
                                    <div class="relative z-10">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-semibold text-white">New Players by Sports Type</h3>
                                        <span class="text-xs uppercase tracking-wider text-white/40">
                                            {{ $usePlayerSportYearSeries ? 'Time Series' : 'Sports type comparison' }}
                                        </span>
                                    </div>
                                    <div class="h-64">
                                        <canvas data-dashboard-chart data-chart-config='@json($playerSportChartConfig)'></canvas>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if($registeredPlayerSportChartConfig)
                            <div class="rounded-2xl p-5 bg-white/5 border border-white/10 relative overflow-hidden group">
                                    <div class="relative z-10">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-semibold text-white">Currently Active Players by Sports Type
                                        </h3>
                                        <span class="text-xs uppercase tracking-wider text-white/40">
                                            {{ $useRegisteredPlayerYearSeries ? 'Time Series' : 'Active registrations' }}
                                        </span>
                                    </div>
                                    <div class="h-64">
                                        <canvas data-dashboard-chart data-chart-config='@json($registeredPlayerSportChartConfig)'></canvas>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endif
                    @endcan
