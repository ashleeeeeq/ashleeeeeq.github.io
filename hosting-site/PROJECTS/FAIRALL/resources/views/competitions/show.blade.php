<x-dashboardlayout name="{{ $name }}" title="Competition Details">
    <div class="mb-8 space-y-6 space-y-5">
        <!-- Header Banner Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="p-4 sm:p-6 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold tracking-tight wrap-break-word" style="font-family: var(--font-header1); color: var(--color-white);">
                            {{ $competition->name }}
                        </h1>
                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full whitespace-nowrap" 
                            style="background-color: rgba(255,204,51,0.15); color: var(--color-accent1);">
                            {{ ucfirst($competition->type) }}
                        </span>
                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full whitespace-nowrap" 
                            style="background-color: rgba(59,130,246,0.15); color: var(--color-info);">
                            {{ ucfirst($competition->scale) }}
                        </span>
                    </div>
                    <p class="mt-2 sm:mt-3 text-xs sm:text-sm" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                        {{ $competition->program?->program_name ?? 'No program' }}
                    </p>
                    <p class="mt-1 text-xs sm:text-sm" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                        {{ $competition->start?->format('M d, Y g:i A') }} — {{ $competition->end?->format('M d, Y g:i A') ?? 'TBD' }}
                    </p>
                    <div class="flex items-center gap-2 mt-1">
                        <svg class="w-4 h-4 shrink-0" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"></path>
                        </svg>
                        <p class="text-xs sm:text-sm font-medium" style="color: rgba(255,255,255,0.85); font-family: var(--font-body1);">{{ $competition->venue }}</p>
                    </div>

                    @if ($competition->description)
                        <div class="mt-3 sm:mt-4 p-3 sm:p-4 rounded-xl" style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
                            <p class="text-xs sm:text-sm leading-5 sm:leading-6" style="color: rgba(255,255,255,0.8); font-family: var(--font-body1);">
                                {{ $competition->description }}
                            </p>
                        </div>
                    @endif
                </div>

                @can('work-on-competitions')
                    <div class="flex flex-wrap gap-2 sm:gap-3 mt-2 lg:mt-0">
                        <a href="{{ route('competition-results.create', $competition) }}" 
                            class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-sm sm:text-base font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                            style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                            onmouseover="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(0.95)'"
                            onmouseout="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(1)'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                            </svg>
                            Add Result
                        </a>
                        <a href="{{ route('competitions.edit', $competition) }}" 
                            class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-sm sm:text-base font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02]"
                            style="font-family: var(--font-body1); background-color: transparent; color: var(--color-info); border: 1px solid var(--color-info);"
                            onmouseover="this.style.backgroundColor='rgba(59,130,246,0.1)'; this.style.color='var(--color-info-dark)'"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-info)'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                            </svg>
                            Edit
                        </a>
                        <button type="button"
                            class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-sm sm:text-base font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02]"
                            style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                            onmouseover="this.style.backgroundColor='rgba(248,113,113,0.15)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)'"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-danger)'; this.style.borderColor='var(--color-danger)'"
                            onclick="document.getElementById('deleteCompetitionModal').showModal()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                            </svg>
                            Delete
                        </button>
                    </div>
                @endcan
            </div>
        </div>

        <!-- Delete Competition Confirmation Modal -->
        <x-confirm-dialog
            id="deleteCompetitionModal"
            title="Delete Competition"
            message="Are you sure you want to delete <strong>{{ $competition->name }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
            deleteUrl="{{ route('competitions.destroy', $competition) }}" />

        @if (session('status'))
            <div class="rounded-xl p-3 sm:p-4 flex items-start gap-2 sm:gap-3 transition-all duration-300"
                style="background-color: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3);">
                <svg class="w-5 h-5 shrink-0 mt-0.5" style="color: var(--color-success);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs sm:text-sm" style="color: rgba(255,255,255,0.9); font-family: var(--font-body1);">{{ session('status') }}</span>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid gap-3 sm:gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl p-4 sm:p-5 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0" style="background-color: rgba(255,204,51,0.15);">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Organizer</div>
                        <div class="text-base sm:text-lg font-semibold mt-1 truncate" style="font-family: var(--font-header1); color: var(--color-white);">
                            {{ $competition->organizer }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl p-4 sm:p-5 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0" style="background-color: rgba(255,204,51,0.15);">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Type</div>
                        <div class="text-base sm:text-lg font-semibold mt-1 truncate" style="font-family: var(--font-header1); color: var(--color-white);">
                            {{ ucfirst($competition->type) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl p-4 sm:p-5 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0" style="background-color: rgba(59,130,246,0.15);">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" style="color: var(--color-info);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6.115 5.19.319 1.913A6 6 0 0 0 8.11 10.36L9.75 12l-.387.775c-.217.433-.132.956.21 1.298l1.348 1.348c.21.21.329.497.329.795v1.089c0 .426.24.815.622 1.006l.153.076c.433.217.956.132 1.298-.21l.723-.723a8.7 8.7 0 0 0 2.288-4.042 1.087 1.087 0 0 0-.358-1.099l-1.33-1.108c-.251-.21-.582-.299-.905-.245l-1.17.195a1.125 1.125 0 0 1-.98-.314l-.295-.295a1.125 1.125 0 0 1 0-1.591l.13-.132a1.125 1.125 0 0 1 1.3-.21l.603.302a.809.809 0 0 0 1.086-1.086L14.25 7.5l1.256-.837a4.5 4.5 0 0 0 1.528-1.732l.146-.292M6.115 5.19A9 9 0 1 0 17.18 4.64M6.115 5.19A8.965 8.965 0 0 1 12 3c1.929 0 3.716.607 5.18 1.645"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Scale</div>
                        <div class="text-base sm:text-lg font-semibold mt-1 truncate" style="font-family: var(--font-header1); color: var(--color-white);">
                            {{ ucfirst($competition->scale) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl p-4 sm:p-5 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0" style="background-color: rgba(16,185,129,0.15);">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" style="color: var(--color-success);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Results Recorded</div>
                        <div class="text-2xl sm:text-3xl font-bold mt-1" style="font-family: var(--font-header1); color: var(--color-white);">
                            {{ $competition->competitionResults->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Table Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Competition Results</h2>
                            <p class="text-xs" style="color: rgba(255,255,255,0.5);">List of all recorded competition results</p>
                        </div>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full" style="background-color: rgba(255,255,255,0.08); color: rgba(255,255,255,0.6);">{{ $competition->competitionResults->count() }} results</span>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Beneficiary</th>
                                <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Placement</th>
                                <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Date Recorded</th>
                                @can('work-on-competitions')
                                    <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Actions</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($competition->competitionResults->sortByDesc('date_given') as $index => $result)
                                <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}" style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                                    <td class="px-4 py-3 text-sm text-left" style="color: rgba(255,255,255,0.85); font-family: var(--font-body1);">
                                        <a href="/beneficiaries/{{ $result->beneficiary?->id }}/profile" 
                                        class="hover:text-accent1 transition-colors duration-200"
                                        style="color: rgba(255,255,255,0.85);"
                                        onmouseover="this.style.color='var(--color-accent1)'"
                                        onmouseout="this.style.color='rgba(255,255,255,0.85)'">
                                            {{ $result->beneficiary?->display_name ?? 'Unknown' }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" 
                                            style="background-color: rgba(255,204,51,0.15); color: var(--color-accent1);">
                                            {{ $result->placement }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">
                                        {{ $result->date_given?->format('M d, Y') }}
                                    </td>
                                    @can('work-on-competitions')
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-1 sm:gap-2">
                                                <!-- Edit Button -->
                                                <button type="button"
                                                        class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-white/10 hover:scale-110"
                                                        title="Edit Result"
                                                        onclick="document.getElementById('editResultModal_{{ $result->id }}').showModal()">
                                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: rgba(255,255,255,0.6);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                                                    </svg>
                                                </button>

                                                <!-- Delete Button -->
                                                <button type="button" 
                                                        class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-red-500/20 hover:scale-110" 
                                                        title="Delete Result"
                                                        onclick="document.getElementById('deleteResultModal_{{ $result->id }}').showModal()">
                                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                                                    </svg>
                                                </button>

                                                <!-- Delete Confirmation Dialog -->
                                                <x-confirm-dialog 
                                                    id="deleteResultModal_{{ $result->id }}" 
                                                    title="Delete Competition Result" 
                                                    message="Are you sure you want to delete this result for <strong>{{ $result->beneficiary?->display_name ?? 'beneficiary' }}</strong> (<strong>{{ $result->placement }}</strong>)? This action cannot be undone."
                                                    deleteUrl="{{ route('competition-results.destroy', [$competition, $result]) }}" />

                                                <!-- Edit Modal -->
                                                <dialog id="editResultModal_{{ $result->id }}" class="fixed inset-0 m-auto rounded-2xl overflow-hidden backdrop:backdrop-blur-sm">
                                                    <div class="rounded-2xl p-0 overflow-hidden max-w-lg mx-auto w-full"
                                                        style="background-color: var(--color-primary1); border: 1px solid rgba(255,255,255,0.08);">
                                                        <!-- Modal Header -->
                                                        <div class="px-6 py-4 border-b"
                                                            style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                                                            <div class="flex items-center gap-3">
                                                                <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                                                                    style="background-color: rgba(255,204,51,0.15);">
                                                                    <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                    </svg>
                                                                </div>
                                                                <div>
                                                                    <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Edit Competition Result</h3>
                                                                    <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Update this competition result</p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Modal Body -->
                                                        <form method="POST" action="{{ route('competition-results.update', [$competition, $result]) }}" class="p-6">
                                                            @csrf
                                                            @method('PUT')

                                                            <div class="space-y-5">
                                                                <!-- Beneficiary -->
                                                                <div>
                                                                    <label class="block mb-2 text-xs font-semibold uppercase tracking-wider"
                                                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">
                                                                        Beneficiary <span style="color: var(--color-danger);">*</span>
                                                                    </label>
                                                                    <div class="relative">
                                                                        <select name="beneficiary_id" required
                                                                            class="w-full rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1 appearance-none cursor-pointer"
                                                                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;" data-choices="true">
                                                                            <option value="" style="color: var(--color-primary1); background-color: var(--color-primary1);">Select beneficiary</option>
                                                                            @foreach ($competition->program?->beneficiaries()->orderBy('last_name')->orderBy('first_name')->get() ?? [] as $beneficiary)
                                                                                <option value="{{ $beneficiary->id }}" style="color: var(--color-primary1); background-color: white;" @selected((string) $result->beneficiary_id === (string) $beneficiary->id)>
                                                                                    {{ $beneficiary->display_name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                                            <svg class="w-4 h-4" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                                                            </svg>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Row: Placement & Date -->
                                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                                    <div>
                                                                        <label class="block mb-2 text-xs font-semibold uppercase tracking-wider"
                                                                            style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">
                                                                            Placement/Achievement <span style="color: var(--color-danger);">*</span>
                                                                        </label>
                                                                        <input type="text" name="placement" value="{{ $result->placement }}"
                                                                            required maxlength="100"
                                                                            class="w-full rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1 text-primary1"
                                                                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                                                                    </div>
                                                                    <div>
                                                                        <label class="block mb-2 text-xs font-semibold uppercase tracking-wider"
                                                                            style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">
                                                                            Date Given
                                                                        </label>
                                                                        <input type="date" name="date_given" value="{{ $result->date_given?->format('Y-m-d') }}"
                                                                            class="w-full rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1 text-primary1"
                                                                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Modal Actions -->
                                                            <div class="flex justify-end gap-3 pt-6 mt-4 border-t" style="border-color: rgba(255,255,255,0.08);">
                                                                <button type="button"
                                                                    onclick="document.getElementById('editResultModal_{{ $result->id }}').close()"
                                                                    class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] text-center inline-flex items-center justify-center gap-2 group"
                                                                    style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                                                                    onmouseover="this.style.backgroundColor='rgba(248,113,113,0.1)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)'"
                                                                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-danger)'; this.style.borderColor='var(--color-danger)'">
                                                                    <svg class="w-4 h-4 transition-all duration-300 group-hover:rotate-90" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                    Cancel
                                                                </button>
                                                                <button type="submit"
                                                                    class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center justify-center gap-2 group"
                                                                    style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                                                                    onmouseover="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(0.95)'"
                                                                    onmouseout="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(1)'">
                                                                    <svg class="w-4 h-4 transition-all duration-300 group-hover:translate-x-1" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M12 4.5v15m7.5-7.5h-15"></path>
                                                                    </svg>
                                                                    Save Changes
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </dialog>
                                            </div>
                                        </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()?->can('work-on-competitions') ? '4' : '3' }}" class="px-4 py-12 text-center">
                                        <svg class="w-12 h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-sm" style="color: rgba(255,255,255,0.4); font-family: var(--font-body1);">No results recorded yet.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="flex justify-end">
            <a href="{{ route('competitions.index') }}" 
               class="inline-flex items-center gap-2 px-4 sm:px-5 py-2 sm:py-2.5 rounded-lg font-semibold transition-all duration-300 hover:bg-white/10 text-sm sm:text-base"
               style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);"
               onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
               onmouseout="this.style.backgroundColor='transparent'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
                Back to Competitions
            </a>
        </div>
    </div>
</x-dashboardlayout>