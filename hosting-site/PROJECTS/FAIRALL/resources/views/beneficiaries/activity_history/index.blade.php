<x-dashboardlayout name="{{ $name }}" title="Activity History">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Activity
                    History</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Track beneficiary activities
                    and events</p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        @include('beneficiaries.partials.profile-tabs')

        <!-- Activity History Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Activity
                                History</h2>
                            <p class="text-xs text-white/50">{{ $beneficiary->display_name }}</p>
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('beneficiaries.activity-history', ['beneficiary' => $beneficiary, 'type' => 'all']) }}"
                            class="filter-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg transition-all duration-200 {{ $filterType === 'all' ? 'active' : '' }}"
                            style="font-family: var(--font-body1); {{ $filterType === 'all' ? 'background-color: var(--color-accent1); color: var(--color-primary1);' : 'background-color: rgba(255,255,255,0.05); color: rgba(255,255,255,0.7);' }} border: 1px solid {{ $filterType === 'all' ? 'var(--color-accent1)' : 'rgba(255,255,255,0.1)' }};">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            All
                        </a>
                        <a href="{{ route('beneficiaries.activity-history', ['beneficiary' => $beneficiary, 'type' => 'activity']) }}"
                            class="filter-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg transition-all duration-200 {{ $filterType === 'activity' ? 'active' : '' }}"
                            style="font-family: var(--font-body1); {{ $filterType === 'activity' ? 'background-color: var(--color-accent1); color: var(--color-primary1);' : 'background-color: rgba(255,255,255,0.05); color: rgba(255,255,255,0.7);' }} border: 1px solid {{ $filterType === 'activity' ? 'var(--color-accent1)' : 'rgba(255,255,255,0.1)' }};">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Activities
                        </a>
                        <a href="{{ route('beneficiaries.activity-history', ['beneficiary' => $beneficiary, 'type' => 'event']) }}"
                            class="filter-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg transition-all duration-200 {{ $filterType === 'event' ? 'active' : '' }}"
                            style="font-family: var(--font-body1); {{ $filterType === 'event' ? 'background-color: var(--color-accent1); color: var(--color-primary1);' : 'background-color: rgba(255,255,255,0.05); color: rgba(255,255,255,0.7);' }} border: 1px solid {{ $filterType === 'event' ? 'var(--color-accent1)' : 'rgba(255,255,255,0.1)' }};">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            Events
                        </a>
                        <a href="{{ route('beneficiaries.activity-history', ['beneficiary' => $beneficiary, 'type' => 'competition']) }}"
                            class="filter-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg transition-all duration-200 {{ $filterType === 'competition' ? 'active' : '' }}"
                            style="font-family: var(--font-body1); {{ $filterType === 'competition' ? 'background-color: var(--color-accent1); color: var(--color-primary1);' : 'background-color: rgba(255,255,255,0.05); color: rgba(255,255,255,0.7);' }} border: 1px solid {{ $filterType === 'competition' ? 'var(--color-accent1)' : 'rgba(255,255,255,0.1)' }};">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35">
                                </path>
                            </svg>
                            Competitions
                        </a>
                        <a href="{{ route('beneficiaries.activity-history', ['beneficiary' => $beneficiary, 'type' => 'home_visit']) }}"
                            class="filter-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-lg transition-all duration-200 {{ $filterType === 'home_visit' ? 'active' : '' }}"
                            style="font-family: var(--font-body1); {{ $filterType === 'home_visit' ? 'background-color: var(--color-accent1); color: var(--color-primary1);' : 'background-color: rgba(255,255,255,0.05); color: rgba(255,255,255,0.7);' }} border: 1px solid {{ $filterType === 'home_visit' ? 'var(--color-accent1)' : 'rgba(255,255,255,0.1)' }};">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25">
                                </path>
                            </svg>
                            Home Visits
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if ($totalRecords > 0)
                    <div class="space-y-3">
                        @foreach ($records as $record)
                            <a href="{{ $record['url'] }}"
                                class="group block rounded-xl p-4 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg"
                                style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 flex-wrap">
                                            @php
                                                $typeColors = [
                                                    'activity' => [
                                                        'bg' => 'rgba(59,130,246,0.15)',
                                                        'text' => '#60a5fa',
                                                    ],
                                                    'event' => ['bg' => 'rgba(16,185,129,0.15)', 'text' => '#34d399'],
                                                    'competition' => [
                                                        'bg' => 'rgba(245,158,11,0.15)',
                                                        'text' => '#fbbf24',
                                                    ],
                                                    'home_visit' => [
                                                        'bg' => 'rgba(139,92,246,0.15)',
                                                        'text' => '#a78bfa',
                                                    ],
                                                ];
                                                $color = $typeColors[$record['type']] ?? [
                                                    'bg' => 'rgba(255,255,255,0.1)',
                                                    'text' => 'rgba(255,255,255,0.6)',
                                                ];
                                            @endphp
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full"
                                                style="background-color: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                                                @if ($record['type'] === 'activity')
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                    </svg>
                                                @elseif ($record['type'] === 'event')
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                @elseif ($record['type'] === 'competition')
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172">
                                                        </path>
                                                    </svg>
                                                @else
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z">
                                                        </path>
                                                    </svg>
                                                @endif
                                                {{ ucfirst(str_replace('_', ' ', $record['type'])) }}
                                            </span>
                                            <h3 class="font-semibold text-white group-hover:text-accent1 transition-colors"
                                                style="font-family: var(--font-header1);">{{ $record['name'] }}</h3>
                                        </div>
                                        <p class="text-sm mt-2" style="color: rgba(255,255,255,0.6);">
                                            {{ $record['details'] }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm whitespace-nowrap"
                                        style="color: rgba(255,255,255,0.4);">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        {{ optional($record['timestamp'])->format('M d, Y') ?? 'N/A' }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if ($totalPages > 1)
                        <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <span class="text-sm" style="color: rgba(255,255,255,0.5);">Showing
                                {{ ($currentPage - 1) * $perPage + 1 }} to
                                {{ min($currentPage * $perPage, $totalRecords) }} of {{ $totalRecords }}
                                records</span>
                            <div class="flex items-center gap-2 flex-wrap">
                                @if ($currentPage > 1)
                                    <a href="{{ route('beneficiaries.activity-history', ['beneficiary' => $beneficiary, 'type' => $filterType, 'page' => $currentPage - 1]) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-lg transition-all duration-200"
                                        style="background-color: rgba(255,255,255,0.05); color: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,0.1);">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                                        </svg>
                                        Previous
                                    </a>
                                @endif

                                @for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                                    <a href="{{ route('beneficiaries.activity-history', ['beneficiary' => $beneficiary, 'type' => $filterType, 'page' => $i]) }}"
                                        class="inline-flex items-center justify-center min-w-9 px-3 py-1.5 text-sm font-medium rounded-lg transition-all duration-200"
                                        style="{{ $i === $currentPage ? 'background-color: var(--color-accent1); color: var(--color-primary1);' : 'background-color: rgba(255,255,255,0.05); color: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,0.1);' }}">
                                        {{ $i }}
                                    </a>
                                @endfor

                                @if ($currentPage < $totalPages)
                                    <a href="{{ route('beneficiaries.activity-history', ['beneficiary' => $beneficiary, 'type' => $filterType, 'page' => $currentPage + 1]) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-lg transition-all duration-200"
                                        style="background-color: rgba(255,255,255,0.05); color: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,0.1);">
                                        Next
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
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
                                d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"></path>
                        </svg>
                        <p class="text-sm" style="color: rgba(255,255,255,0.4);">
                            @if ($filterType === 'all')
                                No activity history found.
                            @else
                                No {{ str_replace('_', ' ', $filterType) }}s found.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-dashboardlayout>
