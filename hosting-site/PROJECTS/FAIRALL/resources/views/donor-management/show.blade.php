<x-dashboardlayout name="{{ $name }}" title="Donor Profile">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight"
                    style="font-family: var(--font-header1); color: var(--color-white);">
                    Donor Profile
                </h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                    View and manage donor information and donation activity
                </p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
        </div>

        @include('donor-management.partials.profile-tabs')

        <div class="grid gap-6 lg:grid-cols-[380px_1fr] mt-8">
            <!-- Left Column - Profile Card -->
            <div class="rounded-2xl overflow-hidden shadow-2xl backdrop-blur-md relative h-fit"
                style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                <div class="absolute top-0 left-0 w-full h-32 opacity-20"
                    style="background: linear-gradient(180deg, var(--color-accent1) 0%, transparent 100%);"></div>

                <div class="relative p-4 sm:p-6">
                    <div class="absolute right-4 sm:right-6 top-4 sm:top-6 flex items-center gap-2">
                        <a href="{{ route('donors.edit', $donor) }}"
                            class="p-2 rounded-xl transition-all duration-300 hover:scale-110 group"
                            style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);"
                            aria-label="Edit donor">
                            <svg class="w-4 h-4 text-white/70 group-hover:text-white transition-colors" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10">
                                </path>
                            </svg>
                        </a>
                        <button type="button" class="p-2 rounded-xl transition-all duration-300 hover:scale-110 group"
                            style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);"
                            aria-label="Delete donor"
                            onclick="document.getElementById('deleteDonorAccountModal').showModal()">
                            <svg class="w-4 h-4 text-red-300 group-hover:text-red-200 transition-colors" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>

                        <x-confirm-dialog id="deleteDonorAccountModal" title="Delete Donor Account"
                            message="Are you sure you want to delete <strong>{{ $donor->display_name }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
                            deleteUrl="{{ route('donors.destroy', $donor) }}" />
                    </div>

                    <div class="flex flex-col items-center text-center gap-4 pt-6">
                        <div class="relative">
                            <div class="absolute inset-0 rounded-full blur-md opacity-50"
                                style="background-color: var(--color-accent1);"></div>
                            <div class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-full overflow-hidden border-4 flex items-center justify-center"
                                style="border-color: var(--color-primary1); background: rgba(255,255,255,0.05);">
                                <span class="text-2xl sm:text-3xl font-bold text-white"
                                    style="font-family: var(--font-header1);">{{ strtoupper(mb_substr($donor->display_name ?? 'D', 0, 1)) }}</span>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-xl sm:text-2xl font-bold text-white tracking-wide"
                                style="font-family: var(--font-header1);">{{ $donor->display_name }}</h2>
                        </div>
                        <div class="flex flex-wrap gap-2 justify-center mt-1">
                            <span
                                class="inline-flex px-2 py-1 sm:px-3 sm:py-1.5 text-xs font-bold rounded-full tracking-wider uppercase border shadow-sm"
                                style="font-family: var(--font-body1); background-color: var(--color-info-light); color: var(--color-info-dark); border-color: rgba(59, 130, 246, 0.2);">
                                {{ ucfirst($donor->donor_type) }} Donor
                            </span>
                        </div>
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
                                    style="font-family: var(--font-body1);">Contact Email</div>
                                <div class="text-white text-xs sm:text-sm font-medium mt-0.5 truncate"
                                    style="font-family: var(--font-body1);">{{ $donor->user?->email ?? 'No email' }}
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex items-start gap-3 sm:gap-4 p-2 sm:p-3 rounded-xl transition-colors hover:bg-white/5">
                            <div class="p-2 sm:p-2.5 rounded-lg shrink-0"
                                style="background-color: rgba(255,255,255,0.05);">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" style="color: var(--color-warning);" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm sm:text-sm font-bold uppercase tracking-widest text-white/40"
                                    style="font-family: var(--font-body1);">Contact Number</div>
                                <div class="text-white text-xs sm:text-sm font-medium mt-0.5 truncate"
                                    style="font-family: var(--font-body1);">{{ $donor->formatted_contact ?? 'N/A' }}
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
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm sm:text-sm font-bold uppercase tracking-widest text-white/40"
                                    style="font-family: var(--font-body1);">Created</div>
                                <div class="text-white text-xs sm:text-sm font-medium mt-0.5 truncate"
                                    style="font-family: var(--font-body1);">
                                    {{ $donor->created_at?->format('M d, Y') ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <div
                            class="flex items-start gap-3 sm:gap-4 p-2 sm:p-3 rounded-xl transition-colors hover:bg-white/5">
                            <div class="p-2 sm:p-2.5 rounded-lg shrink-0"
                                style="background-color: rgba(255,255,255,0.05);">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white/60" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm sm:text-sm font-bold uppercase tracking-widest text-white/40"
                                    style="font-family: var(--font-body1);">Last Updated</div>
                                <div class="text-white text-xs sm:text-sm font-medium mt-0.5 truncate"
                                    style="font-family: var(--font-body1);">
                                    {{ $donor->updated_at?->format('M d, Y') ?? 'N/A' }}</div>
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
                                        @elseif ($activeTab === 'donations')
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z">
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
                                                Donor Metrics
                                            @elseif ($activeTab === 'donations')
                                                Donations
                                            @elseif ($activeTab === 'allocations')
                                                Allocations
                                            @else
                                                Deliverables
                                            @endif
                                        </h3>
                                        <p class="text-sm sm:text-sm font-medium tracking-wider uppercase mt-0.5"
                                            style="font-family: var(--font-body1); color: var(--color-warning);">
                                            @if ($activeTab === 'metrics')
                                                Key performance and activity metrics
                                            @elseif ($activeTab === 'donations')
                                                Recent donation records
                                            @elseif ($activeTab === 'allocations')
                                                Allocation history
                                            @else
                                                Deliverable schedule and progress
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @if ($activeTab === 'donations')
                                <div
                                    class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full sm:w-auto">
                                    <a href="{{ route('donors.donations.index', $donor) }}"
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
                                    <a href="{{ route('donors.donations.create', $donor) }}"
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-[1.02] w-full sm:w-auto"
                                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.5v15m7.5-7.5h-15"></path>
                                        </svg>
                                        Add Donation
                                    </a>
                                </div>
                            @elseif($activeTab === 'allocations')
                                <div
                                    class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full sm:w-auto">
                                    <a href="{{ route('funding.allocations.index', ['donor_id' => $donor->id]) }}"
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
                            @elseif($activeTab === 'deliverables')
                                <div
                                    class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full sm:w-auto">
                                    <a href="{{ route('donors.deliverables.index', $donor) }}"
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-[1.02] w-full sm:w-auto"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.06); color: white; border: 1px solid rgba(255,255,255,0.1);">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        View Gantt Chart
                                    </a>
                                    <a href="{{ route('donors.deliverables.create', $donor) }}"
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
                            <!-- Metrics content -->
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-sm font-semibold text-white/70 mb-4"
                                        style="font-family: var(--font-header1);">Donation Metrics</h4>
                                    <div class="grid gap-4 sm:gap-6 grid-cols-2 md:grid-cols-4">
                                        <div class="rounded-2xl p-4 sm:p-6 text-center transition-all duration-300 hover:bg-white/5 hover:scale-[1.02] relative overflow-hidden group"
                                            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                            <div class="absolute top-0 left-0 w-full h-24 opacity-20 transition-opacity group-hover:opacity-30"
                                                style="background: linear-gradient(180deg, var(--color-info) 0%, transparent 100%);">
                                            </div>
                                            <div class="relative z-10">
                                                <div class="text-xs sm:text-sm font-bold tracking-wide text-white/80 mb-2"
                                                    style="font-family: var(--font-body1);">Last Donation</div>
                                                <div class="text-base sm:text-xl font-bold text-white"
                                                    style="font-family: var(--font-header1);">
                                                    {{ $metrics['last_donation_date']?->format('M d, Y') ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="rounded-2xl p-4 sm:p-6 text-center transition-all duration-300 hover:bg-white/5 hover:scale-[1.02] relative overflow-hidden group"
                                            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                            <div class="absolute top-0 left-0 w-full h-24 opacity-20 transition-opacity group-hover:opacity-30"
                                                style="background: linear-gradient(180deg, var(--color-success) 0%, transparent 100%);">
                                            </div>
                                            <div class="relative z-10">
                                                <div class="text-xs sm:text-sm font-bold tracking-wide text-white/80 mb-2"
                                                    style="font-family: var(--font-body1);">Total Donated</div>
                                                <div class="text-sm sm:text-xl font-bold text-white"
                                                    style="font-family: var(--font-header1);">
                                                    ₱{{ number_format($metrics['total_donated'], 2) }}</div>
                                            </div>
                                        </div>
                                        <div class="rounded-2xl p-4 sm:p-6 text-center transition-all duration-300 hover:bg-white/5 hover:scale-[1.02] relative overflow-hidden group"
                                            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                            <div class="absolute top-0 left-0 w-full h-24 opacity-20 transition-opacity group-hover:opacity-30"
                                                style="background: linear-gradient(180deg, var(--color-warning) 0%, transparent 100%);">
                                            </div>
                                            <div class="relative z-10">
                                                <div class="text-xs sm:text-sm font-bold tracking-wide text-white/80 mb-2"
                                                    style="font-family: var(--font-body1);">Average Donation</div>
                                                <div class="text-sm sm:text-xl font-bold text-white"
                                                    style="font-family: var(--font-header1);">
                                                    ₱{{ number_format($metrics['average_donation'], 2) }}</div>
                                            </div>
                                        </div>
                                        <div class="rounded-2xl p-4 sm:p-6 text-center transition-all duration-300 hover:bg-white/5 hover:scale-[1.02] relative overflow-hidden group"
                                            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                            <div class="absolute top-0 left-0 w-full h-24 opacity-20 transition-opacity group-hover:opacity-30"
                                                style="background: linear-gradient(180deg, var(--color-accent1) 0%, transparent 100%);">
                                            </div>
                                            <div class="relative z-10">
                                                <div class="text-xs sm:text-sm font-bold tracking-wide text-white/80 mb-2"
                                                    style="font-family: var(--font-body1);">Donation Frequency</div>
                                                <div class="text-xs sm:text-xl font-bold text-white"
                                                    style="font-family: var(--font-header1);">
                                                    Every {{ round($metrics['average_days_between_donations']) }}d
                                                </div>
                                                <div class="text-xs text-white/50 mt-1"
                                                    style="font-family: var(--font-body1);">Estimated</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-sm font-semibold text-white/70 mb-4"
                                        style="font-family: var(--font-header1);">Deliverable Metrics</h4>
                                    <div class="grid gap-4 sm:gap-6 grid-cols-1 sm:grid-cols-3">
                                        <div class="rounded-2xl p-4 sm:p-6 text-center transition-all duration-300 hover:bg-white/5 hover:scale-[1.02] relative overflow-hidden group"
                                            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                            <div class="absolute top-0 left-0 w-full h-24 opacity-20 transition-opacity group-hover:opacity-30"
                                                style="background: linear-gradient(180deg, var(--color-danger) 0%, transparent 100%);">
                                            </div>
                                            <div class="relative z-10">
                                                <div class="text-xs sm:text-sm font-bold tracking-wide text-white/80 mb-2"
                                                    style="font-family: var(--font-body1);">Overdue</div>
                                                <div class="text-xl sm:text-2xl font-bold text-white"
                                                    style="font-family: var(--font-header1);">
                                                    {{ $metrics['overdue_deliverables_count'] }}</div>
                                            </div>
                                        </div>
                                        <div class="rounded-2xl p-4 sm:p-6 text-center transition-all duration-300 hover:bg-white/5 hover:scale-[1.02] relative overflow-hidden group"
                                            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                            <div class="absolute top-0 left-0 w-full h-24 opacity-20 transition-opacity group-hover:opacity-30"
                                                style="background: linear-gradient(180deg, var(--color-info) 0%, transparent 100%);">
                                            </div>
                                            <div class="relative z-10">
                                                <div class="text-xs sm:text-sm font-bold tracking-wide text-white/80 mb-2"
                                                    style="font-family: var(--font-body1);">Ongoing</div>
                                                <div class="text-xl sm:text-2xl font-bold text-white"
                                                    style="font-family: var(--font-header1);">
                                                    {{ $metrics['ongoing_deliverables_count'] }}</div>
                                            </div>
                                        </div>
                                        <div class="rounded-2xl p-4 sm:p-6 text-center transition-all duration-300 hover:bg-white/5 hover:scale-[1.02] relative overflow-hidden group"
                                            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                            <div class="absolute top-0 left-0 w-full h-24 opacity-20 transition-opacity group-hover:opacity-30"
                                                style="background: linear-gradient(180deg, var(--color-warning) 0%, transparent 100%);">
                                            </div>
                                            <div class="relative z-10">
                                                <div class="text-xs sm:text-sm font-bold tracking-wide text-white/80 mb-2"
                                                    style="font-family: var(--font-body1);">Next Due</div>
                                                <div class="text-xs sm:text-xl font-bold text-white"
                                                    style="font-family: var(--font-header1);">
                                                    {{ $metrics['next_deliverable_due']?->format('M d, Y') ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-2xl p-6 group transition-all duration-200 hover:scale-[1.02]"
                                    style="background-color: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                                    <div class="flex items-center justify-between gap-4 mb-4">
                                        <div>
                                            <h3 class="text-lg font-bold text-white"
                                                style="font-family: var(--font-header1);">Allocated vs Total Donation
                                                Amount</h3>
                                            <p class="text-white/60 text-sm">Donation funds assigned to beneficiaries
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm text-white/50 uppercase tracking-wider">Used</div>
                                            <div class="text-2xl font-bold text-white">
                                                {{ $metrics['total_allocated_pct'] }}%</div>
                                        </div>
                                    </div>
                                    <div class="w-full h-4 rounded-full overflow-hidden"
                                        style="background-color: rgba(255,255,255,0.08);">
                                        <div class="h-full rounded-full"
                                            style="width: {{ $metrics['total_allocated_pct'] }}%; background-color: var(--color-accent1);">
                                        </div>
                                    </div>
                                    <div class="mt-4 text-sm text-white/70">
                                        ₱{{ number_format($metrics['total_allocated'], 2) }} of
                                        ₱{{ number_format($metrics['total_donated'], 2) }} allocated.
                                        ₱{{ number_format($metrics['remaining_amount'], 2) }} remaining.
                                    </div>
                                </div>
                            </div>
                        @elseif($activeTab === 'donations')
                            @if (($donations ?? null) && $donations->count())
                                <div class="space-y-3">
                                    @foreach ($donations as $index => $donation)
                                        <div class="py-3 sm:py-4 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}"
                                            style="border-color: rgba(255,255,255,0.06);">
                                            <div class="px-3 sm:px-4">
                                                <p class="text-white font-medium text-sm sm:text-base"
                                                    style="font-family: var(--font-body1);">
                                                    {{ $donation->reference_number ?? ($donation->receipt_number ?? 'Donation') }}
                                                </p>
                                                <p class="text-xs sm:text-sm text-white/50 mt-1"
                                                    style="font-family: var(--font-body1);">
                                                    {{ ucfirst($donation->donation_type ?? $donation->status) }} •
                                                    {{ $donation->transaction_date?->format('M d, Y') }}</p>
                                            </div>
                                            <div
                                                class="flex flex-row sm:flex-col items-center justify-between sm:items-end gap-2 px-3 sm:px-4">
                                                <div class="text-white font-semibold text-sm sm:text-base"
                                                    style="font-family: var(--font-body1);">
                                                    ₱{{ number_format((float) $donation->amount, 2) }}</div>
                                                <div class="flex items-center gap-1 sm:gap-2">
                                                    <a href="{{ route('donors.donations.show', [$donor, $donation]) }}"
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
                                                    <a href="{{ route('donors.donations.edit', [$donor, $donation]) }}"
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
                                                        onclick="document.getElementById('deleteDonationModal_{{ $donation->id }}').showModal()">
                                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4"
                                                            style="color: var(--color-danger);" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                    <x-confirm-dialog id="deleteDonationModal_{{ $donation->id }}"
                                                        title="Delete Donation"
                                                        message="Are you sure you want to delete donation <strong>{{ $donation->reference_number }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
                                                        deleteUrl="{{ route('donors.donations.destroy', [$donor, $donation]) }}" />
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if ($donations->hasPages())
                                        <div class="pt-4">
                                            <div class="flex justify-center">
                                                {{ $donations->links('pagination.custom') }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="py-12 text-center">
                                    <p class="text-sm text-white/40" style="font-family: var(--font-body1);">No
                                        donations yet.</p>
                                </div>
                            @endif
                        @elseif($activeTab === 'allocations')
                            @if (($sources ?? null) && $sources->count())
                                <div class="px-4 sm:px-6 pb-3">
                                    <x-bulk-actions-bar bulkUrl="{{ route('funding.allocations.bulk-destroy') }}" tableId="donor-allocations-{{ $donor->id }}" label="allocations" />
                                </div>
                                <div class="space-y-4" data-bulk-table="donor-allocations-{{ $donor->id }}">
                                    <div class="flex items-center gap-2 px-4 py-2 rounded-lg" style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
                                        <input type="checkbox" data-select-all class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                                        <span class="text-sm" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">Select all allocations on page</span>
                                    </div>
                                    @foreach ($sources as $donation)
                                        @php
                                            $totalAllocCents = (int) $donation->allocations->sum('amount_cents');
                                            $totalAllocated = $totalAllocCents / 100;
                                            $balance = (float) $donation->amount - $totalAllocated;
                                        @endphp
                                        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
                                            data-source-type="donation"
                                            data-source-id="{{ $donation->id }}"
                                            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                                            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b flex items-center justify-between gap-3 flex-wrap"
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
                                                        <a href="{{ route('donors.donations.show', [$donor, $donation]) }}"
                                                            class="text-accent1 font-semibold underline underline-offset-2 transition-colors text-sm sm:text-base"
                                                            style="font-family: var(--font-body1);"
                                                            onmouseover="this.style.color='var(--color-accent1-dark)'"
                                                            onmouseout="this.style.color='var(--color-accent1)'">
                                                            {{ $donation->reference_number ?? 'Donation #' . $donation->id }}
                                                        </a>
                                                        <div class="text-xs text-white/50 mt-0.5">
                                                            {{ optional($donation->transaction_date ?? $donation->created_at)->format('M d, Y') }}
                                                        </div>
                                                    </div>
                                                </div>
                                                @php $balanceCents = (int) round($balance * 100); @endphp
                                                @if ($balanceCents <= 0)
                                                    <span
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold shrink-0 cursor-not-allowed opacity-50"
                                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); color: rgba(255,255,255,0.5); border: 1px solid rgba(255,255,255,0.08);"
                                                        title="Fully allocated — no remaining balance">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                                                        </svg>
                                                        Allocate Funds
                                                    </span>
                                                @else
                                                    <a href="{{ route('funding.allocations.create', ['allocation_source' => 'donation', 'donation_id' => $donation->id, 'program_filter' => $donation->program_id ?? '']) }}"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 hover:scale-[1.02] shrink-0"
                                                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                                                        </svg>
                                                        Allocate Funds
                                                    </a>
                                                @endif
                                            </div>
                                            <div class="overflow-x-auto">
                                                <table class="w-full text-sm text-left">
                                                    <thead>
                                                        <tr class="text-white/60 text-xs uppercase tracking-wider border-b"
                                                            style="border-color: rgba(255,255,255,0.06);">
                                                    <th class="px-4 py-2 font-medium text-center"><span class="sr-only">Select</span></th>
                                                    <th class="px-4 py-2 font-medium">Beneficiary</th>
                                                    <th class="px-4 py-2 font-medium">Ref #</th>
                                                             <th class="px-4 py-2 font-medium">Amount</th>
                                                             <th class="px-4 py-2 font-medium">Date Allocated</th>
                                                             <th class="px-4 py-2 font-medium text-center">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody data-poll-target="true">
                                                        @include('funding.allocations._row', ['allocations' => $donation->allocations])
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div data-poll-footer="true" class="px-4 sm:px-6 py-3 flex items-center justify-between gap-4 flex-wrap text-sm"
                                                style="border-top: 1px solid rgba(255,255,255,0.06); background-color: rgba(255,255,255,0.02);">
                                                @include('funding.allocations._footer', ['totalAllocatedCents' => $totalAllocCents, 'balanceCents' => (int) round($balance * 100)])
                                            </div>
                                        </div>
                                    @endforeach
                                    @if ($sources->hasPages())
                                        <div class="pt-4">
                                            <div class="flex justify-center">
                                                {{ $sources->links('pagination.custom') }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="py-12 text-center">
                                    <p class="text-sm text-white/40" style="font-family: var(--font-body1);">No
                                        allocations yet.</p>
                                </div>
                            @endif
                        @elseif($activeTab === 'deliverables')
                            @if (($deliverables ?? null) && $deliverables->count())
                                <div class="space-y-3">
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
                                                    <a href="{{ route('donors.deliverables.show', [$donor, $deliverable]) }}"
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
                                                    <a href="{{ route('donors.deliverables.edit', [$donor, $deliverable]) }}"
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
                                                        onclick="document.getElementById('deleteDeliverableModal_{{ $deliverable->id }}').showModal()">
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
                                                        id="deleteDeliverableModal_{{ $deliverable->id }}"
                                                        title="Delete Deliverable"
                                                        message="Delete this deliverable? This action cannot be undone."
                                                        deleteUrl="{{ route('donors.deliverables.destroy', [$donor, $deliverable]) }}" />
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if ($deliverables->hasPages())
                                        <div class="pt-4">
                                            <div class="flex justify-center">
                                                {{ $deliverables->links('pagination.custom') }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="py-12 text-center">
                                    <p class="text-sm text-white/40" style="font-family: var(--font-body1);">No
                                        deliverables yet.</p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="flex justify-end px-4 sm:px-6 pt-4 mb-8 space-y-6">
        <a href="{{ route('donors.index') }}"
            class="inline-flex items-center gap-2 px-4 sm:px-5 py-2 sm:py-2.5 rounded-lg font-semibold transition-all duration-300 hover:bg-white/10 text-sm sm:text-base"
            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);"
            onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
            onmouseout="this.style.backgroundColor='transparent'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
            </svg>
            Back to Donor Management
        </a>
    </div>

    <!-- Delete Donor Modal -->
    <x-confirm-dialog id="deleteDonorAccountModal" title="Delete Donor Account"
        message="Are you sure you want to delete <strong>{{ $donor->display_name }}</strong>? This record will be moved to the archive and automatically deleted after 90 days." deleteUrl="{{ route('donors.destroy', $donor) }}" />

    <script>
        (function() {
            var cards = document.querySelectorAll('[data-source-type]');
            if (!cards.length) return;

            setInterval(function() {
                var bar = document.getElementById('bulkBar-donor-allocations-{{ $donor->id }}');
                if (bar && !bar.classList.contains('hidden')) return;
                if (document.querySelector('dialog[open]')) return;

                var wrapper = document.querySelector('[data-bulk-table="donor-allocations-{{ $donor->id }}"]');
                var globalCheckedIds = new Set();
                if (wrapper) {
                    wrapper.querySelectorAll('[data-row-checkbox]:checked').forEach(function(cb){ globalCheckedIds.add(cb.value); });
                }

                var pending = [];
                cards.forEach(function(card) {
                    var type = card.getAttribute('data-source-type');
                    var id = card.getAttribute('data-source-id');
                    var target = card.querySelector('[data-poll-target]');
                    var footer = card.querySelector('[data-poll-footer]');
                    if (!target) return;

                    var p = fetch('/funding/allocations/poll?type=' + encodeURIComponent(type) + '&id=' + encodeURIComponent(id))
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            var barNow = document.getElementById('bulkBar-donor-allocations-{{ $donor->id }}');
                            if (barNow && !barNow.classList.contains('hidden')) return;
                            if (document.querySelector('dialog[open]')) return;
                            var currentHasSelection = wrapper && wrapper.querySelectorAll('[data-row-checkbox]:checked').length > 0;
                            if (globalCheckedIds.size === 0 && currentHasSelection) return;

                            target.innerHTML = data.rows;
                            if (footer) footer.innerHTML = data.footer;
                            target.querySelectorAll('[data-row-checkbox]').forEach(function(cb){
                                if (globalCheckedIds.has(cb.value)) cb.checked = true;
                            });
                        })
                        .catch(function() {});
                    pending.push(p);
                });

                Promise.all(pending).then(function(){
                    var wrapper2 = document.querySelector('[data-bulk-table="donor-allocations-{{ $donor->id }}"]');
                    var bar2 = document.getElementById('bulkBar-donor-allocations-{{ $donor->id }}');
                    if (wrapper2 && bar2) {
                        var allChecked = wrapper2.querySelectorAll('[data-row-checkbox]:checked').length;
                        var allEnabled = wrapper2.querySelectorAll('[data-row-checkbox]:not(:disabled)').length;
                        var selectAll = wrapper2.querySelector('[data-select-all]');
                        var countEl = bar2.querySelector('[data-selected-count]');
                        var deleteBtn = bar2.querySelector('[data-bulk-delete-btn]');
                        bar2.classList.toggle('hidden', allChecked === 0);
                        bar2.classList.toggle('flex', allChecked > 0);
                        if (deleteBtn) deleteBtn.disabled = allChecked === 0;
                        if (countEl) countEl.textContent = String(allChecked);
                        if (selectAll) {
                            selectAll.checked = allEnabled > 0 && allChecked === allEnabled;
                            selectAll.indeterminate = allChecked > 0 && allChecked < allEnabled;
                        }
                        var dialog = document.getElementById('bulkConfirm-donor-allocations-{{ $donor->id }}');
                        if (dialog) {
                            var confirmCountEl = dialog.querySelector('[data-bulk-confirm-count]');
                            if (confirmCountEl) confirmCountEl.textContent = String(allChecked);
                        }
                    }
                });
            }, 3000);
        })();
    </script>
</x-dashboardlayout>
