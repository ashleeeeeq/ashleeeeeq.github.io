<x-dashboardlayout name="{{ $name }}" title="Alerts">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Alerts</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Monitoring alerts triggered for this beneficiary</p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        @include('beneficiaries.partials.profile-tabs')

        <!-- Alerts Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                        style="background-color: rgba(255,204,51,0.15);">
                        <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Alerts</h2>
                        <p class="text-xs text-white/50">{{ $beneficiary->display_name }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @php $totalAlerts = $alerts->total(); @endphp
                @if ($totalAlerts > 0)
                    <div class="space-y-3">
                        @foreach ($alerts as $alert)
                            @php
                                $data = json_decode($alert->data);
                                $typeLabel = match ($alert->type) {
                                    'App\Notifications\BeneficiaryAttendanceAlert' => 'Attendance',
                                    'App\Notifications\BeneficiaryGwaAlert' => 'GWA',
                                    'App\Notifications\BeneficiarySchoolAttendanceAlert' => 'School Attendance',
                                    default => 'Alert',
                                };
                                $typeColors = [
                                    'Attendance' => ['bg' => 'rgba(59,130,246,0.15)', 'text' => '#60a5fa'],
                                    'GWA' => ['bg' => 'rgba(245,158,11,0.15)', 'text' => '#fbbf24'],
                                    'School Attendance' => ['bg' => 'rgba(16,185,129,0.15)', 'text' => '#34d399'],
                                ];
                                $color = $typeColors[$typeLabel] ?? ['bg' => 'rgba(255,255,255,0.1)', 'text' => 'rgba(255,255,255,0.6)'];
                            @endphp
                            <div class="group block rounded-xl p-4 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg"
                                style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 flex-wrap">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full"
                                                style="background-color: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                                                </svg>
                                                {{ $typeLabel }}
                                            </span>
                                            <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">
                                                {{ $data->message ?? 'Alert' }}</h3>
                                        </div>
                                        @if (!empty($data->meta))
                                            @php $meta = $data->meta; @endphp
                                            <p class="text-sm mt-2" style="color: rgba(255,255,255,0.6);">
                                                @if ($typeLabel === 'Attendance' && property_exists($meta, 'attendance_rate'))
                                                    Rate: {{ number_format($meta->attendance_rate, 1) }}% &middot; Threshold: {{ number_format($meta->threshold, 1) }}%
                                                @elseif ($typeLabel === 'GWA' && property_exists($meta, 'latest_avg_gwa'))
                                                    Latest GWA: {{ number_format($meta->latest_avg_gwa, 2) }} &middot; Previous: {{ number_format($meta->previous_avg_gwa, 2) }} &middot; Threshold: {{ $meta->threshold }}
                                                @elseif ($typeLabel === 'School Attendance' && property_exists($meta, 'school_attendance'))
                                                    Rate: {{ number_format($meta->school_attendance, 1) }}% &middot; Threshold: {{ number_format($meta->threshold, 1) }}%
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 text-sm whitespace-nowrap"
                                        style="color: rgba(255,255,255,0.4);">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($alert->created_at)->format('M d, Y g:i A') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if ($alerts->lastPage() > 1)
                        <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <span class="text-sm" style="color: rgba(255,255,255,0.5);">Showing
                                {{ $alerts->firstItem() }} to
                                {{ $alerts->lastItem() }} of {{ $alerts->total() }}
                                records</span>
                            <div class="flex items-center gap-2 flex-wrap">
                                @if ($alerts->previousPageUrl())
                                    <a href="{{ $alerts->previousPageUrl() }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-lg transition-all duration-200"
                                        style="background-color: rgba(255,255,255,0.05); color: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,0.1);">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                                        </svg>
                                        Previous
                                    </a>
                                @endif

                                @for ($i = max(1, $alerts->currentPage() - 2); $i <= min($alerts->lastPage(), $alerts->currentPage() + 2); $i++)
                                    <a href="{{ $alerts->url($i) }}"
                                        class="inline-flex items-center justify-center min-w-9 px-3 py-1.5 text-sm font-medium rounded-lg transition-all duration-200"
                                        style="{{ $i === $alerts->currentPage() ? 'background-color: var(--color-accent1); color: var(--color-primary1);' : 'background-color: rgba(255,255,255,0.05); color: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,0.1);' }}">
                                        {{ $i }}
                                    </a>
                                @endfor

                                @if ($alerts->nextPageUrl())
                                    <a href="{{ $alerts->nextPageUrl() }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-lg transition-all duration-200"
                                        style="background-color: rgba(255,255,255,0.05); color: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,0.1);">
                                        Next
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto mb-4" style="color: rgba(255,255,255,0.2);" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"></path>
                        </svg>
                        <p class="text-sm" style="color: rgba(255,255,255,0.4);">No alerts triggered for this beneficiary.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-dashboardlayout>