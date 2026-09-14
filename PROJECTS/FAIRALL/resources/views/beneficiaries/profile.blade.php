<x-dashboardlayout name="{{ $name }}" title="Beneficiaries">
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-white tracking-tight"
                    style="font-family: var(--font-header1);">
                    Beneficiary Profile
                </h1>
                <p class="text-white/60 text-sm mt-2" style="font-family: var(--font-body1);">
                    View and manage beneficiary information and performance metrics
                </p>
                <div class="w-16 h-1 mt-3 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        @include('beneficiaries.partials.profile-tabs')

        <div class="grid gap-6 lg:grid-cols-[380px_1fr] mt-8">
            <!-- Left Column - Profile Card -->
            <div class="rounded-2xl overflow-hidden shadow-2xl backdrop-blur-md relative"
                style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                <div class="absolute top-0 left-0 w-full h-32 opacity-20"
                    style="background: linear-gradient(180deg, var(--color-accent1) 0%, transparent 100%);"></div>

                <div class="relative p-6">
                    <a href="/beneficiaries/{{ $beneficiary->id }}/edit"
                        class="absolute right-6 top-6 p-2 rounded-xl transition-all duration-300 hover:scale-110 group"
                        style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);"
                        aria-label="Edit beneficiary">
                        <svg class="w-4 h-4 text-white/70 group-hover:text-white transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10">
                            </path>
                        </svg>
                    </a>

                    <div class="flex flex-col items-center text-center gap-4 pt-6">
                        <div class="relative">
                            <div class="absolute inset-0 rounded-full blur-md opacity-50"
                                style="background-color: var(--color-accent1);"></div>
                            <div class="relative w-28 h-28 rounded-full overflow-hidden border-4"
                                style="border-color: var(--color-primary1);">
                                <img src="{{ $beneficiary->user->avatar_url ?? asset('images/default-avatar.svg') }}"
                                    alt="Profile picture" class="w-full h-full object-cover">
                            </div>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white tracking-wide"
                                style="font-family: var(--font-header1);">{{ $beneficiary->display_name }}</h2>
                        </div>
                        <div class="flex flex-wrap gap-2 justify-center mt-1">
                            @forelse ($activePrograms as $program)
                                <span
                                    class="inline-flex px-3 py-1.5 text-xs font-bold rounded-full tracking-wider uppercase border shadow-sm"
                                    style="background-color: var(--color-info-light); color: var(--color-info-dark); border-color: rgba(59, 130, 246, 0.2);">
                                    {{ $program->program_name ?? 'Program' }}
                                </span>
                            @empty
                                <span
                                    class="inline-flex px-3 py-1.5 text-xs font-bold rounded-full tracking-wider uppercase border shadow-sm"
                                    style="background-color: rgba(255,255,255,0.05); color: rgba(255,255,255,0.5); border-color: rgba(255,255,255,0.1);">
                                    None
                                </span>
                            @endforelse
                        </div>
                    </div>

                    <div class="w-full h-px my-6"
                        style="background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);">
                    </div>

                    <div class="flex flex-wrap gap-2 justify-center mt-1 mb-5">
                        @forelse ($activeStatuses as $status)
                            <span
                                class="inline-flex px-3 py-1.5 text-xs font-bold rounded-full tracking-wider uppercase border shadow-sm"
                                style="background-color: var(--color-info-light); color: var(--color-info-dark); border-color: rgba(59, 130, 246, 0.2);">
                                {{ $status->statusType->status_name ?? 'Status' }}
                            </span>
                        @empty
                            <span
                                class="inline-flex px-3 py-1.5 text-xs font-bold rounded-full tracking-wider uppercase border shadow-sm"
                                style="background-color: rgba(255,255,255,0.05); color: rgba(255,255,255,0.5); border-color: rgba(255,255,255,0.1);">
                                None
                            </span>
                        @endforelse
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-start gap-4 p-3 rounded-xl transition-colors hover:bg-white/5">
                            <div class="p-2.5 rounded-lg" style="background-color: rgba(255,255,255,0.05);">
                                <svg class="w-5 h-5" style="color: var(--color-info);" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-bold uppercase tracking-widest text-white/40">Grade Level
                                </div>
                                <div class="text-white text-sm font-medium mt-0.5">
                                    {{ $latestEnrollment->grade_level ?? ($latestAcademicRecord->grade_level ?? 'N/A') }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-3 rounded-xl transition-colors hover:bg-white/5">
                            <div class="p-2.5 rounded-lg" style="background-color: rgba(255,255,255,0.05);">
                                <svg class="w-5 h-5" style="color: var(--color-warning);" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-bold uppercase tracking-widest text-white/40">Birth Date
                                </div>
                                <div class="text-white text-sm font-medium mt-0.5">
                                    {{ optional($beneficiary->birth_date)->format('F j, Y') }}</div>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-3 rounded-xl transition-colors hover:bg-white/5">
                            <div class="p-2.5 rounded-lg" style="background-color: rgba(255,255,255,0.05);">
                                <svg class="w-5 h-5 text-white/60" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-bold uppercase tracking-widest text-white/40">Sex</div>
                                <div class="text-white text-sm font-medium mt-0.5">{{ ucfirst($beneficiary->sex) }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-3 rounded-xl transition-colors hover:bg-white/5">
                            <div class="p-2.5 rounded-lg" style="background-color: rgba(255,255,255,0.05);">
                                <svg class="w-5 h-5 text-white/60" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-bold uppercase tracking-widest text-white/40">Contact
                                    Number</div>
                                <div class="text-white text-sm font-medium mt-0.5">{{ $beneficiary->formatted_contact }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-3 rounded-xl transition-colors hover:bg-white/5">
                            <div class="p-2.5 rounded-lg" style="background-color: rgba(255,255,255,0.05);">
                                <svg class="w-5 h-5 text-white/60" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-bold uppercase tracking-widest text-white/40">Address
                                </div>
                                <div class="text-white text-sm font-medium mt-0.5 leading-relaxed">
                                    {{ $addressDisplay !== '' ? $addressDisplay : 'N/A' }}</div>
                            </div>
                        </div>

                        <div class="flex flex-nowrap gap-4 pt-4 px-3 border-t mt-2"
                            style="border-color: rgba(255,255,255,0.08);">
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-bold uppercase tracking-widest text-white/40 mb-1">Form Given</div>
                                <div class="inline-flex items-center gap-1.5 text-sm font-medium {{ $beneficiary->form_given ? 'text-white' : 'text-white/40' }}">
                                    @if ($beneficiary->form_given)
                                        <svg class="w-4 h-4" style="color: var(--color-success);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @endif
                                    {{ $beneficiary->form_given ? 'Yes' : 'No' }}
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-bold uppercase tracking-widest text-white/40 mb-1">With Disability</div>
                                <div class="inline-flex items-center gap-1.5 text-sm font-medium {{ $beneficiary->with_disability ? 'text-white' : 'text-white/40' }}">
                                    @if ($beneficiary->with_disability)
                                        <svg class="w-4 h-4" style="color: var(--color-danger);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @endif
                                    {{ $beneficiary->with_disability ? 'Yes' : 'No' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Analytics-->
            <div class="space-y-6">
                <div class="rounded-2xl overflow-hidden shadow-2xl backdrop-blur-md"
                    style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                    <div class="px-6 py-5 border-b" style="border-color: rgba(255,255,255,0.08);">
                        <div class="flex items-center justify-between gap-4 flex-wrap">
                            <div>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-inner"
                                        style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-white tracking-wide text-lg"
                                            style="font-family: var(--font-header1);">Performance Analytics</h3>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <form method="GET"
                                    action="{{ url('/beneficiaries/' . $beneficiary->id . '/profile') }}"
                                    data-dashboard-period-form class="flex flex-wrap items-end gap-3">
                                    
                                    <div class="w-full sm:w-auto">
                                        <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Period</label>
                                        <select name="period" data-period-select
                                            class="w-full sm:w-auto rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-accent1 transition-all duration-200"
                                            style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); color: white; font-family: var(--font-body1);">
                                            @foreach (['year' => 'Year', 'custom' => 'Custom'] as $value => $label)
                                                <option value="{{ $value }}" style="color: var(--color-primary1); background-color: var(--color-white);" @selected(($filters['period'] ?? 'year') === $value)>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="w-full sm:w-auto {{ ($filters['period'] ?? 'year') === 'custom' ? '' : 'hidden' }}" data-period-field="custom">
                                        <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">From</label>
                                        <input type="date" name="from"
                                            value="{{ $filters['startDate']?->toDateString() ?? now()->subYear()->toDateString() }}"
                                            @disabled(($filters['period'] ?? 'year') !== 'custom')
                                            class="w-full sm:w-auto rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-accent1 transition-all duration-200 text-primary1"
                                            style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); var(--font-body1);">
                                    </div>

                                    <div class="w-full sm:w-auto {{ ($filters['period'] ?? 'year') === 'custom' ? '' : 'hidden' }}" data-period-field="custom">
                                        <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">To</label>
                                        <input type="date" name="to"
                                            value="{{ $filters['endDate']?->toDateString() ?? now()->toDateString() }}"
                                            @disabled(($filters['period'] ?? 'year') !== 'custom')
                                            class="w-full sm:w-auto rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-accent1 transition-all duration-200 text-primary1"
                                            style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15);font-family: var(--font-body1);">
                                    </div>

                                    <div class="w-full sm:w-auto {{ ($filters['period'] ?? 'year') === 'year' ? '' : 'hidden' }}" data-period-field="year">
                                        <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Year</label>
                                        <input type="number" min="2000" max="2100" name="year"
                                            value="{{ $filters['academicYear'] ?? now()->year }}"
                                            @disabled(($filters['period'] ?? 'year') !== 'year')
                                            class="w-full sm:w-24 rounded-xl px-4 py-2 text-primary1 text-sm focus:outline-none focus:ring-2 focus:ring-accent1 transition-all duration-200"
                                            style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); font-family: var(--font-body1);">
                                    </div>

                                    @if ($programType === 'mixed' || $programType === 'none')
                                        <div class="w-full sm:w-auto">
                                            <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Program</label>
                                            <select name="program_id"
                                                class="w-full sm:w-auto rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-accent1 transition-all duration-200"
                                                style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); color: white; font-family: var(--font-body1);">
                                                @foreach ($programs as $program)
                                                    <option value="{{ $program->id }}" style="color: var(--color-primary1); background-color: var(--color-white);" @selected((int) $selectedProgramId === (int) $program->id)>
                                                        {{ $program->program_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div class="w-full sm:w-auto flex gap-2">
                                        <button type="submit" 
                                            class="flex-1 sm:flex-none px-5 py-2 text-sm font-semibold rounded-xl transition-all duration-200 hover:scale-[1.02] whitespace-nowrap"
                                            style="background-color: var(--color-accent1); color: var(--color-primary1); font-family: var(--font-body1);">
                                            Apply
                                        </button>
                                        <a href="{{ url('/beneficiaries/' . $beneficiary->id . '/profile') }}"
                                            class="flex-1 sm:flex-none px-5 py-2 text-sm font-semibold rounded-xl transition-all duration-200 text-center whitespace-nowrap"
                                            style="background-color: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.15); font-family: var(--font-body1);">
                                            Reset
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @if ($analytics['isMultiYear'])
                            @php
                                $isEducation = $selectedProgramId === $educationProgramId;
                                $isSports = $selectedProgramId === $sportsProgramId;
                                $t = $analytics['trends'];
                                $lineOpts = [
                                    'responsive' => true,
                                    'maintainAspectRatio' => false,
                                    'plugins' => ['legend' => ['display' => false]],
                                    'scales' => ['y' => ['beginAtZero' => true, 'max' => 100]],
                                ];
                                $barOpts = [
                                    'responsive' => true,
                                    'maintainAspectRatio' => false,
                                    'plugins' => ['legend' => ['display' => false]],
                                    'scales' => ['y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
                                ];
                                $eqConfig = [
                                    'type' => 'line',
                                    'data' => [
                                        'labels' => $t['eqAttendance']['labels'],
                                        'datasets' => [
                                            [
                                                'label' => 'EQ Attendance %',
                                                'data' => $t['eqAttendance']['data'],
                                                'borderColor' => 'rgba(59, 130, 246, 0.95)',
                                                'backgroundColor' => 'rgba(59, 130, 246, 0.12)',
                                                'tension' => 0.25,
                                                'fill' => true,
                                            ],
                                        ],
                                    ],
                                    'options' => $lineOpts,
                                ];
                                $tutConfig = [
                                    'type' => 'line',
                                    'data' => [
                                        'labels' => $t['tutorialAttendance']['labels'],
                                        'datasets' => [
                                            [
                                                'label' => 'Tutorial Attendance %',
                                                'data' => $t['tutorialAttendance']['data'],
                                                'borderColor' => 'rgba(99, 102, 241, 0.95)',
                                                'backgroundColor' => 'rgba(99, 102, 241, 0.12)',
                                                'tension' => 0.25,
                                                'fill' => true,
                                            ],
                                        ],
                                    ],
                                    'options' => $lineOpts,
                                ];
                                $gwaConfig = [
                                    'type' => 'line',
                                    'data' => [
                                        'labels' => $t['gwa']['labels'],
                                        'datasets' => [
                                            [
                                                'label' => 'GWA',
                                                'data' => $t['gwa']['data'],
                                                'borderColor' => 'rgba(16, 185, 129, 0.95)',
                                                'backgroundColor' => 'rgba(16, 185, 129, 0.12)',
                                                'tension' => 0.25,
                                                'fill' => true,
                                            ],
                                        ],
                                    ],
                                    'options' => $lineOpts,
                                ];
                                $seConfig = [
                                    'type' => 'line',
                                    'data' => [
                                        'labels' => $t['socioEmotional']['labels'],
                                        'datasets' => [
                                            [
                                                'label' => 'Socio-Emotional %',
                                                'data' => $t['socioEmotional']['data'],
                                                'borderColor' => 'rgba(234, 179, 8, 0.95)',
                                                'backgroundColor' => 'rgba(234, 179, 8, 0.12)',
                                                'tension' => 0.25,
                                                'fill' => true,
                                            ],
                                        ],
                                    ],
                                    'options' => $lineOpts,
                                ];
                                $trainingConfig = [
                                    'type' => 'line',
                                    'data' => [
                                        'labels' => $t['trainingAttendance']['labels'],
                                        'datasets' => [
                                            [
                                                'label' => 'Training Attendance %',
                                                'data' => $t['trainingAttendance']['data'],
                                                'borderColor' => 'rgba(16, 185, 129, 0.95)',
                                                'backgroundColor' => 'rgba(16, 185, 129, 0.12)',
                                                'tension' => 0.25,
                                                'fill' => true,
                                            ],
                                        ],
                                    ],
                                    'options' => $lineOpts,
                                ];
                                $uvConfig = [
                                    'type' => 'bar',
                                    'data' => [
                                        'labels' => $t['uniqueVisits']['labels'],
                                        'datasets' => [
                                            [
                                                'label' => 'Unique Visits',
                                                'data' => $t['uniqueVisits']['data'],
                                                'backgroundColor' => 'rgba(244, 114, 182, 0.7)',
                                                'borderColor' => 'rgba(244, 114, 182, 0.95)',
                                                'borderWidth' => 1,
                                            ],
                                        ],
                                    ],
                                    'options' => $barOpts,
                                ];
                            @endphp
                            <!-- Multi-year trend charts -->
                            <div class="grid gap-6 md:grid-cols-2">
                                @if ($isEducation)
                                    <div class="rounded-2xl p-5"
                                        style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                        <h4 class="text-sm font-bold text-white/80 mb-3">EQ Attendance Trend</h4>
                                        <div class="h-48">
                                            <canvas data-dashboard-chart
                                                data-chart-config='@json($eqConfig)'></canvas>
                                        </div>
                                    </div>

                                    <div class="rounded-2xl p-5"
                                        style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                        <h4 class="text-sm font-bold text-white/80 mb-3">Tutorial Attendance Trend</h4>
                                        <div class="h-48">
                                            <canvas data-dashboard-chart
                                                data-chart-config='@json($tutConfig)'></canvas>
                                        </div>
                                    </div>

                                    <div class="rounded-2xl p-5"
                                        style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                        <h4 class="text-sm font-bold text-white/80 mb-3">GWA Trend</h4>
                                        <div class="h-48">
                                            <canvas data-dashboard-chart
                                                data-chart-config='@json($gwaConfig)'></canvas>
                                        </div>
                                    </div>
                                    @if ($staff?->position?->name === 'Social Worker' || $staff?->position?->name === 'Researcher' || $staff?->position?->name === 'System Admin')
                                        <div class="rounded-2xl p-5"
                                            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                            <h4 class="text-sm font-bold text-white/80 mb-3">Socio-Emotional Trend</h4>
                                            <div class="h-48">
                                                <canvas data-dashboard-chart
                                                    data-chart-config='@json($seConfig)'></canvas>
                                            </div>
                                        </div>
                                    @endif
                                @elseif ($isSports)
                                    <div class="rounded-2xl p-5"
                                        style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                        <h4 class="text-sm font-bold text-white/80 mb-3">Training Attendance Trend</h4>
                                        <div class="h-48">
                                            <canvas data-dashboard-chart
                                                data-chart-config='@json($trainingConfig)'></canvas>
                                        </div>
                                    </div>

                                    <div class="rounded-2xl p-5"
                                        style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                        <h4 class="text-sm font-bold text-white/80 mb-3">Unique Visits Trend</h4>
                                        <div class="h-48">
                                            <canvas data-dashboard-chart
                                                data-chart-config='@json($uvConfig)'></canvas>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            @php
                                $isEducation = $selectedProgramId === $educationProgramId;
                                $isSports = $selectedProgramId === $sportsProgramId;
                                $m = $analytics['metrics'];
                            @endphp
                            @if ($isEducation)
                                <!-- First Row - Education -->
                                <div class="grid gap-6 md:grid-cols-2 mb-6">
                                    <!-- EQ Attendance -->
                                    <div class="rounded-2xl p-6 text-center transition-all duration-300 hover:bg-white/5 hover:scale-[1.02] relative overflow-hidden group"
                                        style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                        <div class="absolute top-0 left-0 w-full h-24 opacity-20 transition-opacity group-hover:opacity-30"
                                            style="background: linear-gradient(180deg, var(--color-info) 0%, transparent 100%);">
                                        </div>
                                        <div class="relative w-32 h-32 mx-auto mb-4 drop-shadow-xl z-10">
                                            <svg class="w-32 h-32 -rotate-90 transform">
                                                <circle cx="64" cy="64" r="56" fill="none"
                                                    stroke="rgba(255,255,255,0.05)" stroke-width="8" />
                                                <circle cx="64" cy="64" r="56" fill="none"
                                                    stroke="var(--color-info)" stroke-width="8"
                                                    stroke-linecap="round" stroke-dasharray="351.86"
                                                    stroke-dashoffset="{{ ($m['eqAttendance'] ?? 0) > 0 ? 351.86 - (351.86 * $m['eqAttendance']) / 100 : 351.86 }}"
                                                    class="transition-all duration-1000 ease-out" />
                                            </svg>
                                            <div
                                                class="absolute inset-0 flex flex-col items-center justify-center pt-1">
                                                <svg class="w-6 h-6 mb-1 text-white/70" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-xl font-bold text-white tracking-wide leading-none"
                                                    style="font-family: var(--font-header1);">{{ $m['eqAttendance'] ?? 'N/A' }}{{ isset($m['eqAttendance']) ? '%' : '' }}</span>
                                            </div>
                                        </div>
                                        <div class="text-sm font-bold tracking-wide text-white/80 relative z-10">EQ
                                            Attendance</div>
                                    </div>

                                    <!-- Tutorial Attendance -->
                                    <div class="rounded-2xl p-6 text-center transition-all duration-300 hover:bg-white/5 hover:scale-[1.02] relative overflow-hidden group"
                                        style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                        <div class="absolute top-0 left-0 w-full h-24 opacity-20 transition-opacity group-hover:opacity-30"
                                            style="background: linear-gradient(180deg, var(--color-info) 0%, transparent 100%);">
                                        </div>
                                        <div class="relative w-32 h-32 mx-auto mb-4 drop-shadow-xl z-10">
                                            <svg class="w-32 h-32 -rotate-90 transform">
                                                <circle cx="64" cy="64" r="56" fill="none"
                                                    stroke="rgba(255,255,255,0.05)" stroke-width="8" />
                                                <circle cx="64" cy="64" r="56" fill="none"
                                                    stroke="var(--color-info)" stroke-width="8"
                                                    stroke-linecap="round" stroke-dasharray="351.86"
                                                    stroke-dashoffset="{{ ($m['tutorialAttendance'] ?? 0) > 0 ? 351.86 - (351.86 * $m['tutorialAttendance']) / 100 : 351.86 }}"
                                                    class="transition-all duration-1000 ease-out" />
                                            </svg>
                                            <div
                                                class="absolute inset-0 flex flex-col items-center justify-center pt-1">
                                                <svg class="w-6 h-6 mb-1 text-white/70" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                                </svg>
                                                <span class="text-xl font-bold text-white tracking-wide leading-none"
                                                    style="font-family: var(--font-header1);">{{ $m['tutorialAttendance'] ?? 'N/A' }}{{ isset($m['tutorialAttendance']) ? '%' : '' }}</span>
                                            </div>
                                        </div>
                                        <div class="text-sm font-bold tracking-wide text-white/80 relative z-10">
                                            Tutorial Attendance</div>
                                    </div>

                                </div>

                                <!-- Second Row - Education -->
                                <div class="grid gap-6 md:grid-cols-2 mb-6">
                                    <!-- GWA Score -->
                                    <div class="rounded-2xl p-6 text-center transition-all duration-300 hover:bg-white/5 hover:scale-[1.02] relative overflow-hidden group"
                                        style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                        <div class="absolute top-0 left-0 w-full h-24 opacity-20 transition-opacity group-hover:opacity-30"
                                            style="background: linear-gradient(180deg, var(--color-success) 0%, transparent 100%);">
                                        </div>
                                        <div class="relative w-32 h-32 mx-auto mb-4 drop-shadow-xl z-10">
                                            <svg class="w-32 h-32 -rotate-90 transform">
                                                <circle cx="64" cy="64" r="56" fill="none"
                                                    stroke="rgba(255,255,255,0.05)" stroke-width="8" />
                                                <circle cx="64" cy="64" r="56" fill="none"
                                                    stroke="var(--color-success)" stroke-width="8"
                                                    stroke-linecap="round" stroke-dasharray="351.86"
                                                    stroke-dashoffset="{{ ($m['gwa'] ?? 0) > 0 ? 351.86 - (351.86 * $m['gwa']) / 100 : 351.86 }}"
                                                    class="transition-all duration-1000 ease-out delay-100" />
                                            </svg>
                                            <div
                                                class="absolute inset-0 flex flex-col items-center justify-center pt-1">
                                                <svg class="w-6 h-6 mb-1 text-white/70" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                                                </svg>
                                                <span class="text-xl font-bold text-white tracking-wide leading-none"
                                                    style="font-family: var(--font-header1);">{{ $m['gwa'] ?? 'N/A' }}{{ isset($m['gwa']) ? '%' : '' }}</span>
                                            </div>
                                        </div>
                                        <div class="text-sm font-bold tracking-wide text-white/80 relative z-10">GWA
                                            Score</div>
                                    </div>

                                    <!-- Socio-Emotional -->
                                    @if ($staff?->position?->name == 'Social Worker' || $staff?->position?->name == 'Researcher' || $staff?->position?->name == 'System Admin')
                                        <div class="rounded-2xl p-6 text-center transition-all duration-300 hover:bg-white/5 hover:scale-[1.02] relative overflow-hidden group md:col-span-1 md:col-start-2"
                                            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                            <div class="absolute top-0 left-0 w-full h-24 opacity-20 transition-opacity group-hover:opacity-30"
                                                style="background: linear-gradient(180deg, var(--color-warning) 0%, transparent 100%);">
                                            </div>
                                            <div class="relative w-32 h-32 mx-auto mb-4 drop-shadow-xl z-10">
                                                <svg class="w-32 h-32 -rotate-90 transform">
                                                    <circle cx="64" cy="64" r="56" fill="none"
                                                        stroke="rgba(255,255,255,0.05)" stroke-width="8" />
                                                    <circle cx="64" cy="64" r="56" fill="none"
                                                        stroke="var(--color-warning)" stroke-width="8"
                                                        stroke-linecap="round" stroke-dasharray="351.86"
                                                        stroke-dashoffset="{{ ($m['socioEmotional'] ?? 0) > 0 ? 351.86 - (351.86 * $m['socioEmotional']) / 100 : 351.86 }}"
                                                        class="transition-all duration-1000 ease-out delay-200" />
                                                </svg>
                                                <div
                                                    class="absolute inset-0 flex flex-col items-center justify-center pt-1">
                                                    <svg class="w-6 h-6 mb-1 text-white/70" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                    </svg>
                                                    <span
                                                        class="text-xl font-bold text-white tracking-wide leading-none"
                                                        style="font-family: var(--font-header1);">{{ isset($m['socioEmotional']) ? number_format($m['socioEmotional'], 2) . '%' : 'N/A' }}</span>
                                                </div>
                                            </div>
                                            <div class="text-sm font-bold tracking-wide text-white/80 relative z-10">
                                                Socio-Emotional</div>
                                        </div>
                                    @endif
                                </div>
                            @elseif ($isSports)
                                <!-- First Row - Sports -->
                                <div class="grid gap-6 md:grid-cols-2 mb-6">
                                    <!-- Training Attendance -->
                                    <div class="rounded-2xl p-6 text-center transition-all duration-300 hover:bg-white/5 hover:scale-[1.02] relative overflow-hidden group"
                                        style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                        <div class="absolute top-0 left-0 w-full h-24 opacity-20 transition-opacity group-hover:opacity-30"
                                            style="background: linear-gradient(180deg, var(--color-success) 0%, transparent 100%);">
                                        </div>
                                        <div class="relative w-32 h-32 mx-auto mb-4 drop-shadow-xl z-10">
                                            <svg class="w-32 h-32 -rotate-90 transform">
                                                <circle cx="64" cy="64" r="56" fill="none"
                                                    stroke="rgba(255,255,255,0.05)" stroke-width="8" />
                                                <circle cx="64" cy="64" r="56" fill="none"
                                                    stroke="var(--color-success)" stroke-width="8"
                                                    stroke-linecap="round" stroke-dasharray="351.86"
                                                    stroke-dashoffset="{{ ($m['trainingAttendance'] ?? 0) > 0 ? 351.86 - (351.86 * $m['trainingAttendance']) / 100 : 351.86 }}"
                                                    class="transition-all duration-1000 ease-out" />
                                            </svg>
                                            <div
                                                class="absolute inset-0 flex flex-col items-center justify-center pt-1">
                                                <svg class="w-6 h-6 mb-1 text-white/70" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-xl font-bold text-white tracking-wide leading-none"
                                                    style="font-family: var(--font-header1);">{{ $m['trainingAttendance'] ?? 'N/A' }}{{ isset($m['trainingAttendance']) ? '%' : '' }}</span>
                                            </div>
                                        </div>
                                        <div class="text-sm font-bold tracking-wide text-white/80 relative z-10">
                                            Training Attendance</div>
                                    </div>

                                    <!-- Unique Visits -->
                                    <div class="rounded-2xl p-6 text-center transition-all duration-300 hover:bg-white/5 hover:scale-[1.02] relative overflow-hidden group"
                                        style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                        <div class="absolute top-0 left-0 w-full h-24 opacity-20 transition-opacity group-hover:opacity-30"
                                            style="background: linear-gradient(180deg, var(--color-warning) 0%, transparent 100%);">
                                        </div>
                                        <div class="relative flex flex-col items-center justify-center mb-4 z-10 pt-4">
                                            <div class="w-20 h-20 rounded-2xl flex items-center justify-center"
                                                style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                                                <svg class="w-10 h-10" style="color: var(--color-warning);"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                                </svg>
                                            </div>
                                            <span class="text-4xl font-bold text-white tracking-wide mt-3 leading-none"
                                                style="font-family: var(--font-header1);">{{ $m['uniqueVisits'] ?? 0 }}</span>
                                        </div>
                                        <div class="text-sm font-bold tracking-wide text-white/80 relative z-10">Unique
                                            Visits</div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboardlayout>
