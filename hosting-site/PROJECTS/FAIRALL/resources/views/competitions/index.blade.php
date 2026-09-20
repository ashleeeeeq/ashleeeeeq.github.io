<x-dashboardlayout name="{{ $name }}" title="Competitions">
    <div class="mb-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight"
                    style="font-family: var(--font-header1); color: var(--color-white);">Competitions</h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">Track
                    competition events and recorded results</p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
            @can('create-competitions')
                <a href="{{ route('competitions.create') }}"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap"
                    style="background-color: var(--color-accent1); color: var(--color-primary1); font-family: var(--font-body1);"
                    onmouseover="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(0.95)'"
                    onmouseout="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(1)'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15">
                        </path>
                    </svg>
                    Create Competition
                </a>
            @endcan
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl flex items-center gap-3"
                style="background-color: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3);">
                <svg class="w-5 h-5 shrink-0" style="color: var(--color-success);" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm"
                    style="color: var(--color-success); font-family: var(--font-body1);">{{ session('status') }}</span>
            </div>
        @endif

        <!-- Filter Tabs -->
        <div class="flex gap-2 mb-6">
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'upcoming', 'competitions_page' => 1]) }}"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ ($currentFilter ?? 'upcoming') === 'upcoming' ? 'text-white' : 'text-white/60 hover:text-white' }}"
                style="{{ ($currentFilter ?? 'upcoming') === 'upcoming' ? 'background-color: var(--color-accent1); color: var(--color-primary1);' : 'background-color: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);' }}">
                <span class="inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                    </svg>
                    Upcoming
                </span>
            </a>
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'completed', 'competitions_page' => 1]) }}"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ ($currentFilter ?? 'upcoming') === 'completed' ? 'text-white' : 'text-white/60 hover:text-white' }}"
                style="{{ ($currentFilter ?? 'upcoming') === 'completed' ? 'background-color: var(--color-accent1); color: var(--color-primary1);' : 'background-color: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);' }}">
                <span class="inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z">
                        </path>
                    </svg>
                    Completed
                </span>
            </a>
        </div>

        <!-- Search Bar -->
        <div class="mb-6">
            <form method="GET" action="">
                <div class="relative max-w-md">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color: var(--color-primary1);"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input type="hidden" name="filter" value="{{ $currentFilter }}">
                    <input type="text" name="search" placeholder="Search competitions..."
                        value="{{ request('search') }}" class="w-full rounded-xl pl-10 pr-4 py-2 text-sm"
                        style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: var(--color-primary1);">
                </div>
            </form>
        </div>

        <div class="grid gap-4 sm:gap-5 grid-cols-1 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($competitions as $competition)
                <a href="{{ route('competitions.show', $competition) }}"
                    class="group rounded-2xl overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-xl flex"
                    style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                    <!-- Vertical Accent Line -->
                    <div class="w-1 rounded-l-2xl shrink-0" style="background: var(--color-accent1);"></div>

                    <div class="p-4 sm:p-5 space-y-3 sm:space-y-4 flex-1">
                        <div class="flex items-start justify-between gap-2 sm:gap-3">
                            <div class="flex-1 min-w-0">
                                <h2 class="text-base sm:text-lg font-bold tracking-tight wrap-break-word"
                                    style="font-family: var(--font-header1); color: var(--color-white);">
                                    {{ $competition->name }}</h2>
                                <p class="text-xs sm:text-sm mt-1 wrap-break-word"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">
                                    {{ $competition->program?->program_name ?? 'No program' }}</p>
                            </div>
                            <span
                                class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full whitespace-nowrap shrink-0"
                                style="background-color: rgba(255,204,51,0.15); color: var(--color-accent1);">
                                {{ ucfirst($competition->type) }}
                            </span>
                            <span
                                class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full whitespace-nowrap shrink-0"
                                style="background-color: {{ (!$competition->end || $competition->end >= now()) ? 'rgba(16,185,129,0.15)' : 'rgba(255,255,255,0.05)' }}; color: {{ (!$competition->end || $competition->end >= now()) ? 'var(--color-success)' : 'rgba(255,255,255,0.5)' }};">
                                {{ (!$competition->end || $competition->end >= now()) ? 'Upcoming' : 'Completed' }}
                            </span>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-lg flex items-center justify-center shrink-0"
                                    style="background-color: rgba(255,204,51,0.15);">
                                    <svg class="w-3 h-3" style="color: var(--color-accent1);" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z">
                                        </path>
                                    </svg>
                                </div>
                                <span class="text-xs sm:text-sm wrap-break-word"
                                    style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $competition->venue }}</span>
                            </div>
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-lg flex items-center justify-center shrink-0"
                                    style="background-color: rgba(59,130,246,0.15);">
                                    <svg class="w-3 h-3" style="color: var(--color-info);" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5">
                                        </path>
                                    </svg>
                                </div>
                                <span class="text-xs sm:text-sm wrap-break-word"
                                    style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $competition->start?->format('M d, Y g:i A') }}</span>
                            </div>
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-lg flex items-center justify-center shrink-0"
                                    style="background-color: rgba(139,92,246,0.15);">
                                    <svg class="w-3 h-3" style="color: #a78bfa;" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="m6.115 5.19.319 1.913A6 6 0 0 0 8.11 10.36L9.75 12l-.387.775c-.217.433-.132.956.21 1.298l1.348 1.348c.21.21.329.497.329.795v1.089c0 .426.24.815.622 1.006l.153.076c.433.217.956.132 1.298-.21l.723-.723a8.7 8.7 0 0 0 2.288-4.042 1.087 1.087 0 0 0-.358-1.099l-1.33-1.108c-.251-.21-.582-.299-.905-.245l-1.17.195a1.125 1.125 0 0 1-.98-.314l-.295-.295a1.125 1.125 0 0 1 0-1.591l.13-.132a1.125 1.125 0 0 1 1.3-.21l.603.302a.809.809 0 0 0 1.086-1.086L14.25 7.5l1.256-.837a4.5 4.5 0 0 0 1.528-1.732l.146-.292M6.115 5.19A9 9 0 1 0 17.18 4.64M6.115 5.19A8.965 8.965 0 0 1 12 3c1.929 0 3.716.607 5.18 1.64">
                                        </path>
                                    </svg>
                                </div>
                                <span class="text-xs sm:text-sm wrap-break-word"
                                    style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ ucfirst($competition->scale) }}
                                    scale</span>
                            </div>
                        </div>

                        <div class="flex flex-col xs:flex-row xs:items-center justify-between gap-2 pt-3 border-t"
                            style="border-color: rgba(255,255,255,0.08); font-family: var(--font-body1);">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: rgba(255,255,255,0.5);"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-xs sm:text-sm"
                                    style="color: rgba(255,255,255,0.6);">{{ $competition->competition_results_count }}
                                    result/s</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: rgba(255,255,255,0.5);"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z">
                                    </path>
                                </svg>
                                <span class="text-xs sm:text-sm truncate max-w-25 xs:max-w-none"
                                    style="color: rgba(255,255,255,0.6);">{{ $competition->creator?->display_name ?? 'Staff' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-1">
                            <span class="text-sm font-semibold transition-colors group-hover:text-accent1"
                                style="font-family: var(--font-body1); color: var(--color-accent1);">View
                                Details</span>
                            <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1"
                                style="color: var(--color-accent1);" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                            </svg>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full rounded-2xl p-8 sm:p-12 text-center"
                    style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                    <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-4" style="color: rgba(255,255,255,0.2);"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0">
                        </path>
                    </svg>
                    <p class="text-sm" style="color: rgba(255,255,255,0.4); font-family: var(--font-body1);">No
                        competitions found.</p>
                </div>
            @endforelse
        </div>

        @if ($competitions->hasPages())
            <div class="flex justify-center mt-8">
                {{ $competitions->links('pagination.custom') }}
            </div>
        @endif
    </div>
</x-dashboardlayout>
