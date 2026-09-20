<x-dashboardlayout name="{{ $name }}" title="Activities">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight"
                    style="font-family: var(--font-header1); color: var(--color-white);">Activities</h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">View active
                    programs, sports, and participation activity</p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
            @can('create-activities')
                <a href="/activities/create"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap"
                    style="background-color: var(--color-accent1); color: var(--color-primary1); font-family: var(--font-body1);"
                    onmouseover="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(0.95)'"
                    onmouseout="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(1)'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15">
                        </path>
                    </svg>
                    Create Activity
                </a>
            @endcan
        </div>

        <!-- Success Message -->
        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl flex items-center gap-3"
                style="background-color: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3);">
                <svg class="w-5 h-5 shrink-0" style="color: var(--color-success);" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm"
                    style="color: var(--color-success); font-family: var(--font-body1);">{{ session('status') }}</span>
            </div>
        @endif

        <!-- Filter Tabs -->
        <div class="flex gap-2 mb-6">
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'active', 'activities_page' => 1]) }}"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ ($currentFilter ?? 'active') === 'active' ? 'text-white' : 'text-white/60 hover:text-white' }}"
                style="{{ ($currentFilter ?? 'active') === 'active' ? 'background-color: var(--color-accent1); color: var(--color-primary1);' : 'background-color: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);' }}">
                <span class="inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                    </svg>
                    Active
                </span>
            </a>
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'inactive', 'activities_page' => 1]) }}"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ ($currentFilter ?? 'active') === 'inactive' ? 'text-white' : 'text-white/60 hover:text-white' }}"
                style="{{ ($currentFilter ?? 'active') === 'inactive' ? 'background-color: var(--color-accent1); color: var(--color-primary1);' : 'background-color: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);' }}">
                <span class="inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z">
                        </path>
                    </svg>
                    Inactive
                </span>
            </a>
        </div>

        <!-- Search Bar -->
        <div class="mb-6">
            <form method="GET" action="">
                <input type="hidden" name="filter" value="{{ $currentFilter }}">
                <div class="relative max-w-md">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color: var(--color-primary1);"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input type="text" name="search" placeholder="Search activities..."
                        value="{{ request('search') }}" class="w-full rounded-xl pl-10 pr-4 py-2 text-sm"
                        style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: var(--color-primary1);">
                </div>
            </form>
        </div>

        <!-- Activities Grid -->
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($activities as $index => $activity)
                <a href="/activities/{{ $activity->id }}"
                    class="group rounded-2xl overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-xl flex"
                    style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                    <!-- Vertical Accent Line -->
                    <div class="w-1 rounded-l-2xl shrink-0" style="background: var(--color-accent1);"></div>

                    <div class="p-5 space-y-4 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <h2 class="text-lg font-bold tracking-tight wrap-break-word"
                                    style="font-family: var(--font-header1); color: var(--color-white);">
                                    {{ $activity->name }}</h2>
                                <p class="text-xs mt-1 wrap-break-word"
                                    style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                                    {{ $activity->program?->program_name ?? 'N/A' }}</p>
                            </div>
                            <span
                                class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full whitespace-nowrap shrink-0"
                                style="background-color: {{ $activity->is_active ? 'rgba(16,185,129,0.15)' : 'rgba(255,255,255,0.05)' }}; color: {{ $activity->is_active ? 'var(--color-success)' : 'rgba(255,255,255,0.5)' }};">
                                {{ $activity->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0"
                                    style="background-color: rgba(255,204,51,0.15);">
                                    <svg class="w-3 h-3" style="color: var(--color-accent1);" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </div>
                                <span class="text-sm wrap-break-word"
                                    style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $activity->activityType?->name ?? 'N/A' }}</span>
                            </div>
                            @if ($activity->sportType?->name)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0"
                                        style="background-color: rgba(59,130,246,0.15);">
                                        <svg class="w-3 h-3" style="color: var(--color-info);" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z">
                                            </path>
                                        </svg>
                                    </div>
                                    <span class="text-sm wrap-break-word"
                                        style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $activity->sportType?->name ?? 'No sport type' }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-between text-sm pt-3 border-t"
                            style="border-color: rgba(255,255,255,0.08); font-family: var(--font-body1);">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" style="color: rgba(255,255,255,0.5);" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span style="color: rgba(255,255,255,0.6);">{{ $activity->activity_sessions_count }}
                                    sessions</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" style="color: rgba(255,255,255,0.5);" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                                <span
                                    style="color: rgba(255,255,255,0.6);">{{ $activity->program?->program_name ?? 'Program' }}</span>
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
                <div class="col-span-full rounded-2xl p-12 text-center"
                    style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                    <svg class="w-16 h-16 mx-auto mb-4" style="color: rgba(255,255,255,0.2);" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <p class="text-sm" style="color: rgba(255,255,255,0.4); font-family: var(--font-body1);">No
                        activities found.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($activities->hasPages())
            <div class="flex justify-center mt-8">
                {{ $activities->links('pagination.custom') }}
            </div>
        @endif
    </div>
</x-dashboardlayout>
