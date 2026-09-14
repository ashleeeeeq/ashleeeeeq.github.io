<x-dashboardlayout name="{{ $name }}" title="Grant Details">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight"
                    style="font-family: var(--font-header1); color: var(--color-white);">
                    Grant Details
                </h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                    View and manage grant information and disbursement activity
                </p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="overflow-x-auto overflow-y-hidden mb-6">
            <div class="flex gap-1 border-b" style="border-color: rgba(255,255,255,0.08);">
                <a href="/grants/{{ $grant->id }}?tab=metrics"
                    class="tab-item inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 {{ ($activeTab ?? 'metrics') === 'metrics' ? 'text-accent1 border-b-2 border-accent1' : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}"
                    style="font-family: var(--font-body1);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.5 19.5h15m-15-4.5h10.5m-10.5-4.5h15m-15-4.5h7.5"></path>
                    </svg>
                    Metrics
                </a>

                <a href="/grants/{{ $grant->id }}?tab=allocations"
                    class="tab-item inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 {{ ($activeTab ?? '') === 'allocations' ? 'text-accent1 border-b-2 border-accent1' : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}"
                    style="font-family: var(--font-body1);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v7.5m2.25-6.466a9.016 9.016 0 0 0-3.461-.203c-.536.072-.974.478-1.021 1.017a4.559 4.559 0 0 0-.018.402c0 .464.336.844.775.994l2.95 1.012c.44.15.775.53.775.994 0 .136-.006.27-.018.402-.047.539-.485.945-1.021 1.017a9.077 9.077 0 0 1-3.461-.203M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z">
                        </path>
                    </svg>
                    Allocations
                </a>

                <a href="/grants/{{ $grant->id }}?tab=deliverables"
                    class="tab-item inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 {{ ($activeTab ?? '') === 'deliverables' ? 'text-accent1 border-b-2 border-accent1' : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}"
                    style="font-family: var(--font-body1);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0Z"></path>
                    </svg>
                    Deliverables
                </a>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[380px_1fr]">
            <!-- Left Column - Grant Info Card -->
            <div class="rounded-2xl overflow-hidden shadow-2xl backdrop-blur-md relative h-fit"
                style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                <div class="absolute top-0 left-0 w-full h-32 opacity-20"
                    style="background: linear-gradient(180deg, var(--color-accent1) 0%, transparent 100%);"></div>

                <div class="relative p-4 sm:p-6">
                    <div class="absolute right-4 sm:right-6 top-4 sm:top-6 flex items-center gap-2">
                        <a href="/grants/{{ $grant->id }}/edit"
                            class="p-2 rounded-xl transition-all duration-300 hover:scale-110 group"
                            style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);"
                            aria-label="Edit grant">
                            <svg class="w-4 h-4 text-white/70 group-hover:text-white transition-colors" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10">
                                </path>
                            </svg>
                        </a>
                        <button type="button" class="p-2 rounded-xl transition-all duration-300 hover:scale-110 group"
                            style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);"
                            aria-label="Delete grant" onclick="document.getElementById('deleteGrantModal').showModal()">
                            <svg class="w-4 h-4 text-red-300 group-hover:text-red-200 transition-colors" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>

                        <x-confirm-dialog id="deleteGrantModal" title="Delete Grant"
                            message="Are you sure you want to delete the grant <strong>{{ $grant->grant_name }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
                            deleteUrl="/grants/{{ $grant->id }}" />
                    </div>

                    <div class="flex flex-col items-center text-center gap-4 pt-6">
                        <div class="relative">
                            <div class="absolute inset-0 rounded-full blur-md opacity-50"
                                style="background-color: var(--color-accent1);"></div>
                            <div class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-full overflow-hidden border-4 flex items-center justify-center"
                                style="border-color: var(--color-primary1); background: rgba(255,255,255,0.05);">
                                <span class="text-2xl sm:text-3xl font-bold text-white"
                                    style="font-family: var(--font-header1);">{{ strtoupper(mb_substr($grant->grant_name ?? 'G', 0, 1)) }}</span>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-xl sm:text-2xl font-bold text-white tracking-wide"
                                style="font-family: var(--font-header1);">{{ $grant->grant_name }}</h2>
                        </div>
                        @if ($grant->programs->count())
                            <div class="flex flex-wrap gap-2 justify-center mt-1">
                                @foreach ($grant->programs as $program)
                                    <span
                                        class="inline-flex px-2 py-1 sm:px-3 sm:py-1.5 text-xs font-bold rounded-full tracking-wider uppercase"
                                        style="font-family: var(--font-body1); background-color: rgba(255,204,51,0.15); color: var(--color-accent1);">
                                        {{ $program->program_name ?? 'Program' }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span
                                class="inline-flex px-2 py-1 sm:px-3 sm:py-1.5 text-xs font-bold rounded-full tracking-wider uppercase"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); color: rgba(255,255,255,0.6);">
                                No linked programs
                            </span>
                        @endif
                    </div>

                    <div class="w-full h-px my-4 sm:my-6"
                        style="background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);">
                    </div>

                    <div class="space-y-3 sm:space-y-4">
                        <div
                            class="flex items-start gap-3 sm:gap-4 p-2 sm:p-3 rounded-xl transition-colors hover:bg-white/5">
                            <div class="p-2 sm:p-2.5 rounded-lg shrink-0"
                                style="background-color: rgba(255,255,255,0.05);">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" style="color: var(--color-info);" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm sm:text-sm font-bold uppercase tracking-widest text-white/40"
                                    style="font-family: var(--font-body1);">Organization</div>
                                <div class="text-white text-xs sm:text-sm font-medium mt-0.5 truncate"
                                    style="font-family: var(--font-body1);">
                                    {{ $grant->organization_name ?? 'No organization' }}</div>
                            </div>
                        </div>

                        <div
                            class="flex items-start gap-3 sm:gap-4 p-2 sm:p-3 rounded-xl transition-colors hover:bg-white/5">
                            <div class="p-2 sm:p-2.5 rounded-lg shrink-0"
                                style="background-color: rgba(255,255,255,0.05);">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" style="color: var(--color-success);"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V5m0 11v-2m6-3a6 6 0 11-12 0 6 6 0 0112 0Z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm sm:text-sm font-bold uppercase tracking-widest text-white/40"
                                    style="font-family: var(--font-body1);">Total Amount</div>
                                <div class="text-white text-xs sm:text-sm font-medium mt-0.5 truncate"
                                    style="font-family: var(--font-body1);">
                                    ₱{{ number_format((float) $grant->total_amount, 2) }}</div>
                            </div>
                        </div>

                        <div
                            class="flex items-start gap-3 sm:gap-4 p-2 sm:p-3 rounded-xl transition-colors hover:bg-white/5">
                            <div class="p-2 sm:p-2.5 rounded-lg shrink-0"
                                style="background-color: rgba(255,255,255,0.05);">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white/60" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm sm:text-sm font-bold uppercase tracking-widest text-white/40"
                                    style="font-family: var(--font-body1);">Period</div>
                                <div class="text-white text-xs sm:text-sm font-medium mt-0.5"
                                    style="font-family: var(--font-body1);">
                                    {{ optional($grant->start_date)->format('M d, Y') ?? 'N/A' }} -
                                    {{ optional($grant->end_date)->format('M d, Y') ?? 'ongoing' }}
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex items-start gap-3 sm:gap-4 p-2 sm:p-3 rounded-xl transition-colors hover:bg-white/5">
                            <div class="p-2 sm:p-2.5 rounded-lg shrink-0"
                                style="background-color: rgba(255,255,255,0.05);">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white/60" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm sm:text-sm font-bold uppercase tracking-widest text-white/40"
                                    style="font-family: var(--font-body1);">Created</div>
                                <div class="text-white text-xs sm:text-sm font-medium mt-0.5 truncate"
                                    style="font-family: var(--font-body1);">
                                    {{ $grant->created_at?->format('M d, Y') ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Tab Content -->
            <div class="space-y-6">
                <div class="rounded-2xl overflow-hidden shadow-2xl backdrop-blur-md"
                    style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                    <div class="px-4 sm:px-6 py-4 sm:py-5 border-b" style="border-color: rgba(255,255,255,0.08);">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 flex-wrap">
                            <div>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shadow-inner"
                                        style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                                        @if ($activeTab === 'metrics')
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75">
                                                </path>
                                            </svg>
                                        @elseif ($activeTab === 'allocations')
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v7.5m2.25-6.466a9.016 9.016 0 0 0-3.461-.203c-.536.072-.974.478-1.021 1.017a4.559 4.559 0 0 0-.018.402c0 .464.336.844.775.994l2.95 1.012c.44.15.775.53.775.994 0 .136-.006.27-.018.402-.047.539-.485.945-1.021 1.017a9.077 9.077 0 0 1-3.461-.203M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z">
                                                </path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z">
                                                </path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-white text-base sm:text-lg"
                                            style="font-family: var(--font-header1);">
                                            @if ($activeTab === 'metrics')
                                                Grant Metrics
                                            @elseif ($activeTab === 'allocations')
                                                Allocations
                                            @else
                                                Deliverables
                                            @endif
                                        </h3>
                                        <p class="text-sm sm:text-sm font-medium tracking-wider uppercase mt-0.5"
                                            style="font-family: var(--font-body1); color: var(--color-warning);">
                                            @if ($activeTab === 'metrics')
                                                An overview of grant metrics
                                            @elseif ($activeTab === 'allocations')
                                                Funds allocated to beneficiaries
                                            @else
                                                Deliverable schedule and progress
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @if ($activeTab === 'allocations')
                                <div
                                    class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full sm:w-auto">
                                    <a href="{{ route('funding.allocations.index', ['grant_id' => $grant->id]) }}"
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-[1.02] w-full sm:w-auto"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.06); color: white; border: 1px solid rgba(255,255,255,0.1);">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v7.5m2.25-6.466a9.016 9.016 0 0 0-3.461-.203c-.536.072-.974.478-1.021 1.017a4.559 4.559 0 0 0-.018.402c0 .464.336.844.775.994l2.95 1.012c.44.15.775.53.775.994 0 .136-.006.27-.018.402-.047.539-.485.945-1.021 1.017a9.077 9.077 0 0 1-3.461-.203M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z">
                                            </path>
                                        </svg>
                                        View All
                                    </a>
                                </div>
                            @elseif ($activeTab === 'deliverables')
                                <div
                                    class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full sm:w-auto">
                                    <a href="{{ route('grants.deliverables.index', $grant) }}"
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-[1.02] w-full sm:w-auto"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.06); color: white; border: 1px solid rgba(255,255,255,0.1);">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        View Gantt Chart
                                    </a>
                                    <a href="{{ route('grants.deliverables.create', $grant) }}"
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-[1.02] w-full sm:w-auto"
                                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.5v15m7.5-7.5h-15"></path>
                                        </svg>
                                        Add Deliverable
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="p-4 sm:p-6">
                        @if ($activeTab === 'metrics')
                            @php $allocationPct = $totalGrantCents > 0 ? min(100, round(($totalAllocatedCents / $totalGrantCents) * 100)) : 0; @endphp
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-sm font-semibold text-white/70 mb-4"
                                        style="font-family: var(--font-header1);">Grant Metrics</h4>
                                    @php $nextDeliverable = $grant->deliverables()->where('progress', '<', 100)->where('end_date', '>', now())->orderBy('end_date')->first(); @endphp
                                    <div class="grid gap-4 sm:gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                                        <div class="rounded-2xl p-4 sm:p-6 text-center transition-all duration-300 hover:bg-white/5 hover:scale-[1.02] relative overflow-hidden group"
                                            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                            <div class="absolute top-0 left-0 w-full h-24 opacity-20 transition-opacity group-hover:opacity-30"
                                                style="background: linear-gradient(180deg, var(--color-danger) 0%, transparent 100%);">
                                            </div>
                                            <div class="relative z-10">
                                                <div class="text-xs sm:text-sm font-bold tracking-wide text-white/80 mb-2"
                                                    style="font-family: var(--font-body1);">Overdue Deliverables</div>
                                                <div class="text-xl sm:text-2xl font-bold text-white"
                                                    style="font-family: var(--font-header1);">
                                                    {{ $grant->deliverables()->where('progress', '<', 100)->where('end_date', '<', now())->count() }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="rounded-2xl p-4 sm:p-6 text-center transition-all duration-300 hover:bg-white/5 hover:scale-[1.02] relative overflow-hidden group"
                                            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                            <div class="absolute top-0 left-0 w-full h-24 opacity-20 transition-opacity group-hover:opacity-30"
                                                style="background: linear-gradient(180deg, var(--color-info) 0%, transparent 100%);">
                                            </div>
                                            <div class="relative z-10">
                                                <div class="text-xs sm:text-sm font-bold tracking-wide text-white/80 mb-2"
                                                    style="font-family: var(--font-body1);">Ongoing Deliverables</div>
                                                <div class="text-xl sm:text-2xl font-bold text-white"
                                                    style="font-family: var(--font-header1);">
                                                    {{ $grant->deliverables()->where('progress', '<', 100)->where('end_date', '>', now())->count() }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="rounded-2xl p-4 sm:p-6 text-center transition-all duration-300 hover:bg-white/5 hover:scale-[1.02] relative overflow-hidden group"
                                            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                            <div class="absolute top-0 left-0 w-full h-24 opacity-20 transition-opacity group-hover:opacity-30"
                                                style="background: linear-gradient(180deg, var(--color-accent1) 0%, transparent 100%);">
                                            </div>
                                            <div class="relative z-10">
                                                <div class="text-xs sm:text-sm font-bold tracking-wide text-white/80 mb-2"
                                                    style="font-family: var(--font-body1);">Next Deliverable Due</div>
                                                <div class="text-lg sm:text-xl font-bold text-white leading-tight"
                                                    style="font-family: var(--font-header1);">
                                                    @if ($nextDeliverable)
                                                        {{ $nextDeliverable->title }}
                                                    @else
                                                        <span class="text-sm font-normal text-white/50">None</span>
                                                    @endif
                                                </div>
                                                @if ($nextDeliverable)
                                                    <div class="text-xs text-white/50 mt-1"
                                                        style="font-family: var(--font-body1);">
                                                        Due {{ $nextDeliverable->end_date->format('M d, Y') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="rounded-2xl p-4 sm:p-6 text-center transition-all duration-300 hover:bg-white/5 hover:scale-[1.02] relative overflow-hidden group"
                                            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                            <div class="absolute top-0 left-0 w-full h-24 opacity-20 transition-opacity group-hover:opacity-30"
                                                style="background: linear-gradient(180deg, var(--color-success) 0%, transparent 100%);">
                                            </div>
                                            <div class="relative z-10">
                                                <div class="text-xs sm:text-sm font-bold tracking-wide text-white/80 mb-2"
                                                    style="font-family: var(--font-body1);">Beneficiaries Supported
                                                </div>
                                                <div class="text-xl sm:text-2xl font-bold text-white"
                                                    style="font-family: var(--font-header1);">
                                                    {{ $beneficiariesSupported }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="rounded-2xl p-6 group transition-all duration-200 hover:scale-[1.02] mt-6"
                                        style="background-color: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                                        <div class="flex items-center justify-between gap-4 mb-4">
                                            <div>
                                                <h3 class="text-lg font-bold text-white"
                                                    style="font-family: var(--font-header1);">Allocated vs Total Grant
                                                    Amount</h3>
                                                <p class="text-white/60 text-sm">Grant funds assigned to beneficiaries
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm text-white/50 uppercase tracking-wider">Used</div>
                                                <div class="text-2xl font-bold text-white">{{ $allocationPct }}%</div>
                                            </div>
                                        </div>
                                        <div class="w-full h-4 rounded-full overflow-hidden"
                                            style="background-color: rgba(255,255,255,0.08);">
                                            <div class="h-full rounded-full"
                                                style="width: {{ $allocationPct }}%; background-color: var(--color-accent1);">
                                            </div>
                                        </div>
                                        <div class="mt-4 text-sm text-white/70">
                                            ₱{{ number_format($totalAllocatedCents / 100, 2) }} of
                                            ₱{{ number_format($totalGrantCents / 100, 2) }} allocated.
                                            ₱{{ number_format($remainingCents / 100, 2) }} remaining.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($activeTab === 'allocations')
                            @if (($allocations ?? null) && $allocations->count())
                                <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
                                    data-source-type="grant"
                                    data-source-id="{{ $grant->id }}"
                                    style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                                    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b flex items-center justify-between gap-3"
                                        style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                                        <div class="flex items-center gap-3">
                                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center"
                                                style="background-color: rgba(255,204,51,0.15);">
                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v7.5m2.25-6.466a9.016 9.016 0 0 0-3.461-.203c-.536.072-.974.478-1.021 1.017a4.559 4.559 0 0 0-.018.402c0 .464.336.844.775.994l2.95 1.012c.44.15.775.53.775.994 0 .136-.006.27-.018.402-.047.539-.485.945-1.021 1.017a9.077 9.077 0 0 1-3.461-.203M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div>
                                                <span class="font-semibold text-white text-sm sm:text-base"
                                                    style="font-family: var(--font-body1);">Allocation Breakdown</span>
                                                <div class="text-xs text-white/50 mt-0.5" style="font-family: var(--font-body1);">
                                                    {{ $allocations->count() }} allocation{{ $allocations->count() !== 1 ? 's' : '' }}
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ route('funding.allocations.create', ['allocation_source' => 'grant', 'grant_id' => $grant->id, 'program_filter' => $grant->programs->first()?->id ?? '']) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 hover:scale-[1.02] shrink-0"
                                            style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                                            </svg>
                                            Allocate Funds
                                        </a>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm text-left">
                                            <thead>
                                                <tr class="text-white/60 text-xs uppercase tracking-wider border-b"
                                                    style="border-color: rgba(255,255,255,0.06); font-family: var(--font-body1);">
                                                    <th class="px-4 py-2 font-medium">Beneficiary</th>
                                                     <th class="px-4 py-2 font-medium">Amount</th>
                                                     <th class="px-4 py-2 font-medium">Date Allocated</th>
                                                     <th class="px-4 py-2 font-medium text-center">Actions</th>
                                                 </tr>
                                             </thead>
                                             <tbody data-poll-target="true">
                                                 @include('funding.allocations._row', ['allocations' => $allocations])
                                             </tbody>
                                        </table>
                                    </div>
                                    <div data-poll-footer="true" class="px-4 sm:px-6 py-3 flex items-center justify-between gap-4 flex-wrap text-sm"
                                        style="border-top: 1px solid rgba(255,255,255,0.06); background-color: rgba(255,255,255,0.02);">
                                        @include('funding.allocations._footer', ['totalAllocatedCents' => $totalAllocatedCents, 'balanceCents' => $remainingCents])
                                    </div>
                                </div>
                            @else
                                <div class="py-12 text-center">
                                    <p class="text-sm text-white/40" style="font-family: var(--font-body1);">No
                                        allocations yet.</p>
                                </div>
                            @endif
                        @elseif($activeTab === 'deliverables')
                            <div class="space-y-3">
                                @if (($deliverables ?? null) && $deliverables->count())
                                    @foreach ($deliverables as $index => $deliverable)
                                        <div class="py-3 sm:py-4 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}"
                                            style="border-color: rgba(255,255,255,0.06);">
                                            <div class="px-3 sm:px-4">
                                                <p class="text-white font-medium text-sm sm:text-base"
                                                    style="font-family: var(--font-body1);">{{ $deliverable->title }}
                                                </p>
                                                <p class="text-xs sm:text-sm text-white/50 mt-1"
                                                    style="font-family: var(--font-body1);">
                                                    {{ $deliverable->description }}</p>
                                            </div>
                                            <div
                                                class="flex flex-row sm:flex-col items-center justify-between sm:items-end gap-2 px-3 sm:px-4">
                                                <div class="text-xs sm:text-sm text-white/50"
                                                    style="font-family: var(--font-body1);">
                                                    {{ optional($deliverable->start_date)->format('M d, Y') }} -
                                                    {{ optional($deliverable->end_date)->format('M d, Y') }}</div>
                                                <div class="flex items-center gap-1 sm:gap-2">
                                                    <a href="{{ route('grants.deliverables.show', [$grant, $deliverable]) }}"
                                                        class="p-1.5 sm:p-2 rounded-lg hover:bg-white/10 transition-all"
                                                        title="View">
                                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4"
                                                            style="color: rgba(255,255,255,0.6);" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z">
                                                            </path>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                            </path>
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('grants.deliverables.edit', [$grant, $deliverable]) }}"
                                                        class="p-1.5 sm:p-2 rounded-lg hover:bg-white/10 transition-all"
                                                        title="Edit">
                                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4"
                                                            style="color: rgba(255,255,255,0.6);" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10">
                                                            </path>
                                                        </svg>
                                                    </a>
                                                    <button type="button"
                                                        class="p-1.5 sm:p-2 rounded-lg hover:bg-red-500/20 transition-all"
                                                        title="Delete"
                                                        onclick="document.getElementById('deleteGrantDeliverableModal_{{ $deliverable->id }}').showModal()">
                                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4"
                                                            style="color: var(--color-danger);" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                                                            </path>
                                                        </svg>
                                                    </button>

                                                    <x-confirm-dialog
                                                        id="deleteGrantDeliverableModal_{{ $deliverable->id }}"
                                                        title="Delete Deliverable"
                                                        message="Delete this deliverable? This action cannot be undone."
                                                        deleteUrl="{{ route('grants.deliverables.destroy', [$grant, $deliverable]) }}" />
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if ($deliverables->hasPages())
                                        <div class="flex justify-center pt-6 mt-4">
                                            {{ $deliverables->links('pagination.custom') }}
                                        </div>
                                    @endif
                                @else
                                    <div class="py-12 text-center">
                                        <p class="text-sm text-white/40" style="font-family: var(--font-body1);">No
                                            deliverables for this grant yet.</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            var cards = document.querySelectorAll('[data-source-type]');
            if (!cards.length) return;

            setInterval(function() {
                cards.forEach(function(card) {
                    var type = card.getAttribute('data-source-type');
                    var id = card.getAttribute('data-source-id');
                    var target = card.querySelector('[data-poll-target]');
                    var footer = card.querySelector('[data-poll-footer]');
                    if (!target) return;

                    fetch('/funding/allocations/poll?type=' + encodeURIComponent(type) + '&id=' + encodeURIComponent(id))
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            target.innerHTML = data.rows;
                            if (footer) footer.innerHTML = data.footer;
                        })
                        .catch(function() {});
                });
            }, 3000);
        })();
    </script>

    <!-- Back Button -->
    <div class="flex justify-end px-4 sm:px-6 pt-4 mb-8 space-y-6">
        <a href="/grants"
            class="inline-flex items-center gap-2 px-4 sm:px-5 py-2 sm:py-2.5 rounded-lg font-semibold transition-all duration-300 hover:bg-white/10 text-sm sm:text-base"
            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);"
            onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
            onmouseout="this.style.backgroundColor='transparent'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
            </svg>
            Back to Grant Management
        </a>
    </div>
</x-dashboardlayout>
