<x-dashboardlayout name="{{ $name }}" title="Injury Record Details">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Injury Record Details</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Beneficiary: <span class="font-semibold text-white">{{ $beneficiary->display_name }}</span></p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        <!-- Injury Record Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Injury Record #{{ $record->id }}</h2>
                            <p class="text-xs text-white/50">Detailed injury and recovery information</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="/beneficiaries/{{ $beneficiary->id }}/injury-records/{{ $record->id }}/edit" 
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold transition-all duration-200 hover:scale-[1.02]"
                           style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                            </svg>
                            Edit
                        </a>
                        <a href="/beneficiaries/{{ $beneficiary->id }}/injury-records" 
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold transition-all duration-200 hover:bg-white/10"
                           style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                            </svg>
                            Back
                        </a>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Injury Type -->
                    <div class="rounded-xl p-4 transition-all hover:bg-white/5" style="border: 1px solid rgba(255,255,255,0.1);">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                                <svg class="w-3 h-3" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Injury Type</p>
                        </div>
                        <p class="text-base font-semibold text-white" style="font-family: var(--font-body1);">{{ $record->injury_type }}</p>
                    </div>

                    <!-- Body Part -->
                    <div class="rounded-xl p-4 transition-all hover:bg-white/5" style="border: 1px solid rgba(255,255,255,0.1);">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                                <svg class="w-3 h-3" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z"></path>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Body Part</p>
                        </div>
                        <p class="text-base font-semibold text-white" style="font-family: var(--font-body1);">{{ $record->body_part }}</p>
                    </div>

                    <!-- Severity -->
                    <div class="rounded-xl p-4 transition-all hover:bg-white/5" style="border: 1px solid rgba(255,255,255,0.1);">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                                <svg class="w-3 h-3" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Severity</p>
                        </div>
                        @php
                            $severityColors = [
                                'minor' => ['bg' => 'rgba(16,185,129,0.15)', 'color' => 'var(--color-success)'],
                                'moderate' => ['bg' => 'rgba(255,204,51,0.15)', 'color' => 'var(--color-accent1)'],
                                'serious' => ['bg' => 'rgba(245,158,11,0.15)', 'color' => 'var(--color-warning)'],
                                'severe' => ['bg' => 'rgba(248,113,113,0.15)', 'color' => 'var(--color-danger)'],
                                'critical' => ['bg' => 'rgba(220,38,38,0.15)', 'color' => 'var(--color-danger-dark)'],
                            ];
                            $severityColor = $severityColors[$record->severity] ?? ['bg' => 'rgba(255,255,255,0.1)', 'color' => 'rgba(255,255,255,0.7)'];
                        @endphp
                        <span class="inline-flex px-3 py-1.5 text-sm font-semibold rounded-full mt-1" 
                              style="background-color: {{ $severityColor['bg'] }}; color: {{ $severityColor['color'] }};">
                            {{ ucfirst($record->severity) }}
                        </span>
                    </div>

                    <!-- Status -->
                    <div class="rounded-xl p-4 transition-all hover:bg-white/5" style="border: 1px solid rgba(255,255,255,0.1);">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                                <svg class="w-3 h-3" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Status</p>
                        </div>
                        @php
                            $statusColors = [
                                'recovering' => ['bg' => 'rgba(255,204,51,0.15)', 'color' => 'var(--color-accent1)'],
                                'recovered' => ['bg' => 'rgba(16,185,129,0.15)', 'color' => 'var(--color-success)'],
                                'chronic' => ['bg' => 'rgba(59,130,246,0.15)', 'color' => 'var(--color-info)'],
                            ];
                            $statusColor = $statusColors[$record->status] ?? ['bg' => 'rgba(255,255,255,0.1)', 'color' => 'rgba(255,255,255,0.7)'];
                        @endphp
                        <span class="inline-flex px-3 py-1.5 text-sm font-semibold rounded-full mt-1" 
                              style="background-color: {{ $statusColor['bg'] }}; color: {{ $statusColor['color'] }};">
                            {{ ucfirst($record->status) }}
                        </span>
                    </div>

                    <!-- Recovery Start Date -->
                    <div class="rounded-xl p-4 transition-all hover:bg-white/5" style="border: 1px solid rgba(255,255,255,0.1);">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                                <svg class="w-3 h-3" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"></path>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Recovery Start</p>
                        </div>
                        <p class="text-base font-semibold text-white" style="font-family: var(--font-body1);">{{ \Carbon\Carbon::parse($record->recovery_start_date)->format('F d, Y') }}</p>
                    </div>

                    <!-- Recovery End Date -->
                    <div class="rounded-xl p-4 transition-all hover:bg-white/5" style="border: 1px solid rgba(255,255,255,0.1);">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                                <svg class="w-3 h-3" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5M15 6.75H9"></path>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Recovery End</p>
                        </div>
                        <p class="text-base font-semibold text-white" style="font-family: var(--font-body1);">
                            {{ $record->recovery_end_date ? \Carbon\Carbon::parse($record->recovery_end_date)->format('F d, Y') : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboardlayout>