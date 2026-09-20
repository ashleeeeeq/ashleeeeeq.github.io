<x-dashboardlayout name="{{ $name }}" title="Home Visit Details">
    <div class="mb-8 space-y-6 space-y-5 ">
        <!-- Header Banner Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="p-4 sm:p-6 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold tracking-tight break-words" style="font-family: var(--font-header1); color: var(--color-white);">
                            {{ $homeVisit->beneficiary?->display_name ?? 'Unknown' }}
                        </h1>
                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full whitespace-nowrap" 
                            style="background-color: rgba(255,204,51,0.15); color: var(--color-accent1);">
                            {{ ucfirst(str_replace('-', ' ', $homeVisit->visit_type)) }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 mt-3">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"></path>
                        </svg>
                        <p class="text-xs sm:text-sm" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                            Schedule: {{ $homeVisit->schedule?->format('F d, Y g:i A') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 mt-1">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"></path>
                        </svg>
                        <p class="text-xs sm:text-sm font-medium" style="color: rgba(255,255,255,0.85); font-family: var(--font-body1);">
                            Purpose: {{ $homeVisit->purpose }}
                        </p>
                    </div>
                </div>

                @can('manage-home-visits', $homeVisit)
                    <div class="flex flex-wrap gap-2 sm:gap-3 mt-2 lg:mt-0">
                        <a href="{{ route('home-visits.edit', $homeVisit) }}" 
                            class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-sm sm:text-base font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02]"
                            style="font-family: var(--font-body1); background-color: transparent; color: var(--color-info); border: 1px solid var(--color-info);"
                            onmouseover="this.style.backgroundColor='rgba(59,130,246,0.1)'; this.style.color='var(--color-info-dark)'"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-info)'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                            </svg>
                            Edit
                        </a>
                        @can('manage-home-visits')
                            <button type="button" 
                                class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-sm sm:text-base font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02]"
                                style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                                onmouseover="this.style.backgroundColor='rgba(248,113,113,0.15)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)'"
                                onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-danger)'; this.style.borderColor='var(--color-danger)'"
                                onclick="document.getElementById('deleteHomeVisitModal').showModal()">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                                </svg>
                                Delete
                            </button>

                            <x-confirm-dialog 
                                id="deleteHomeVisitModal" 
                                title="Delete Home Visit" 
                                message="Are you sure you want to delete the home visit for <strong>{{ $homeVisit->beneficiary?->display_name ?? 'Unknown' }}</strong> on <strong>{{ $homeVisit->schedule?->format('M d, Y') }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
                                deleteUrl="{{ route('home-visits.destroy', $homeVisit) }}" />
                        @endcan
                    </div>
                @endcan
            </div>
        </div>

        @if (session('status'))
            <div class="rounded-xl p-3 sm:p-4 flex items-start gap-2 sm:gap-3 transition-all duration-300"
                style="background-color: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3);">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--color-success);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs sm:text-sm" style="color: rgba(255,255,255,0.9); font-family: var(--font-body1);">{{ session('status') }}</span>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid gap-4 sm:gap-5 grid-cols-1 md:grid-cols-2">
            <div class="rounded-2xl p-5 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                        <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white" style="font-family: var(--font-header1);">Visit Information</h3>
                </div>
                <div class="space-y-3 text-sm" style="font-family: var(--font-body1);">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: rgba(255,255,255,0.5);">Visit Type</div>
                        <div class="font-medium" style="color: rgba(255,255,255,0.85);">{{ ucfirst(str_replace('-', ' ', $homeVisit->visit_type)) }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: rgba(255,255,255,0.5);">Scheduled For</div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" style="color: rgba(255,255,255,0.4);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"></path>
                            </svg>
                            <div class="font-medium" style="color: rgba(255,255,255,0.85);">{{ $homeVisit->schedule?->format('F d, Y g:i A') }}</div>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: rgba(255,255,255,0.5);">Beneficiary Address</div>
                        <div class="font-medium" style="color: rgba(255,255,255,0.85);">{{ $homeVisit->beneficiary->address->full_address ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: rgba(255,255,255,0.5);">Purpose</div>
                        <div class="font-medium" style="color: rgba(255,255,255,0.85);">{{ $homeVisit->purpose }}</div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl p-5 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(59,130,246,0.15);">
                        <svg class="w-4 h-4" style="color: var(--color-info);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white" style="font-family: var(--font-header1);">Metadata</h3>
                </div>
                <div class="space-y-3 text-sm" style="font-family: var(--font-body1);">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: rgba(255,255,255,0.5);">Assigned Staff</div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" style="color: rgba(255,255,255,0.4);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
                            </svg>
                            <div class="font-medium" style="color: rgba(255,255,255,0.85);">{{ $homeVisit->assignedStaff?->display_name ?? 'Unassigned' }}</div>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: rgba(255,255,255,0.5);">Created By</div>
                        <div class="font-medium" style="color: rgba(255,255,255,0.85);">{{ $homeVisit->creator?->display_name ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: rgba(255,255,255,0.5);">Created At</div>
                        <div class="font-medium" style="color: rgba(255,255,255,0.85);">{{ $homeVisit->created_at?->format('M d, Y g:i A') }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: rgba(255,255,255,0.5);">Modified By</div>
                        <div class="font-medium" style="color: rgba(255,255,255,0.85);">{{ $homeVisit->updator?->display_name ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: rgba(255,255,255,0.5);">Last Updated</div>
                        <div class="font-medium" style="color: rgba(255,255,255,0.85);">{{ $homeVisit->updated_at?->format('M d, Y g:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>

        @if ($homeVisit->notes)
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9zm3.75 11.625a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Notes</h3>
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Additional visit details and observations</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-sm leading-relaxed" style="color: rgba(255,255,255,0.8); font-family: var(--font-body1);">
                        {{ $homeVisit->notes }}
                    </p>
                </div>
            </div>
        @endif

        <!-- Back Button -->
        <div class="flex justify-end pt-4">
            <a href="{{ route('home-visits.index') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold transition-all duration-300 hover:bg-white/10"
               style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);"
               onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
               onmouseout="this.style.backgroundColor='transparent'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
                Back to Home Visits
            </a>
        </div>
    </div>
</x-dashboardlayout>