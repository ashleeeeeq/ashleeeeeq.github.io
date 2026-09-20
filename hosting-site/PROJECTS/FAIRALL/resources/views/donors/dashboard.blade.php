<x-dashboardlayout name="{{ $name }}" title="Dashboard">
    <div class="mb-8 space-y-6">
        <!-- Hero / Welcome Section -->
        <div class="rounded-2xl overflow-hidden relative"
            style="background: linear-gradient(135deg, rgba(255,204,51,0.08) 0%, rgba(255,255,255,0.02) 100%); border: 1px solid rgba(255,255,255,0.08);">
            <div class="absolute top-0 right-0 w-32 sm:w-48 md:w-64 h-32 sm:h-48 md:h-64 opacity-10"
                style="background: radial-gradient(circle, var(--color-accent1) 0%, transparent 70%);"></div>
            <div class="relative p-4 sm:p-6 md:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex-1">
                        <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold tracking-tight wrap-break-word"
                            style="font-family: var(--font-header1); color: var(--color-white);">
                            Welcome back, {{ $name }}!
                        </h1>
                        <p class="text-xs sm:text-sm mt-1 sm:mt-2"
                            style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                            Track your donations and impact with our organization
                        </p>
                        <div class="w-16 sm:w-20 h-1 mt-2 sm:mt-3 rounded-full"
                            style="background: var(--color-accent1);"></div>
                    </div>
                    <div class="flex items-center gap-2 self-start sm:self-center">
                        <a href="{{ route('donor.portal.donate') }}"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap"
                            style="background-color: var(--color-accent1); color: var(--color-primary1); font-family: var(--font-body1);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.5v15m7.5-7.5h-15"></path>
                            </svg>
                            Make a Donation
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @php
            $countThresholds = [10, 25, 50, 100, 250, 500, 1000];
            $amountThresholds = [50000, 100000, 250000, 500000, 1000000, 2500000, 5000000];

            $nextCountMilestone = null;
            foreach ($countThresholds as $t) {
                if ($totalCompletedCount < $t) {
                    $nextCountMilestone = $t;
                    break;
                }
            }

            $nextAmountMilestone = null;
            foreach ($amountThresholds as $t) {
                if ($totalCompletedAmount < $t) {
                    $nextAmountMilestone = $t;
                    break;
                }
            }

            $prevCount = $nextCountMilestone
                ? $countThresholds[array_search($nextCountMilestone, $countThresholds) - 1] ?? 0
                : $totalCompletedCount;
            $countProgress = $nextCountMilestone
                ? (($totalCompletedCount - $prevCount) / ($nextCountMilestone - $prevCount)) * 100
                : 100;

            $prevAmount = $nextAmountMilestone
                ? $amountThresholds[array_search($nextAmountMilestone, $amountThresholds) - 1] ?? 0
                : $totalCompletedAmount;
            $amountProgress = $nextAmountMilestone
                ? (($totalCompletedAmount - $prevAmount) / ($nextAmountMilestone - $prevAmount)) * 100
                : 100;

            $tenureDecimal = $firstDonationDate ? max(0, round($firstDonationDate->diffInMonths(now()) / 12, 2)) : 0;
            $nextTenureMilestone = (int) (floor($tenureDecimal) + 1);
            $tenureProgress = $firstDonationDate ? ($tenureDecimal - floor($tenureDecimal)) * 100 : 0;
        @endphp

        <!-- Stats Cards Grid -->
        <div class="grid gap-5 grid-cols-1 lg:grid-cols-2 xl:grid-cols-4">
            <!-- Donation Count Milestone Card -->
            <div class="rounded-2xl p-5 transition-all duration-300 hover:scale-[1.02] hover:shadow-[0_8px_30px_rgba(0,0,0,0.2)]"
                style="background: linear-gradient(135deg, rgba(16,185,129,0.12), rgba(255,255,255,0.03)); border: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                        style="background-color: rgba(16,185,129,0.2);">
                        <svg class="w-5 h-5" style="color: #10b981;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">
                            Donations Made</p>
                        <p class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">
                            {{ number_format($totalCompletedCount) }}</p>
                    </div>
                </div>
                @if ($nextCountMilestone)
                    <div class="mt-1">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span style="color: rgba(255,255,255,0.5);">Next: {{ $nextCountMilestone }}th
                                Donation</span>
                            <span style="color: rgba(255,255,255,0.5);">{{ number_format($totalCompletedCount) }} /
                                {{ $nextCountMilestone }}</span>
                        </div>
                        <div class="w-full h-1.5 rounded-full" style="background-color: rgba(255,255,255,0.08);">
                            <div class="h-1.5 rounded-full transition-all duration-500"
                                style="width: {{ min(100, $countProgress) }}%; background-color: #10b981;"></div>
                        </div>
                    </div>
                @else
                    <p class="text-xs mt-1" style="color: var(--color-accent1);">🏆 {{ end($countThresholds) }}+
                        Donations</p>
                @endif
            </div>

            <!-- Donation Total Milestone Card -->
            <div class="rounded-2xl p-5 transition-all duration-300 hover:scale-[1.02] hover:shadow-[0_8px_30px_rgba(0,0,0,0.2)]"
                style="background: linear-gradient(135deg, rgba(255,204,51,0.12), rgba(255,255,255,0.03)); border: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                        style="background-color: rgba(255,204,51,0.2);">
                        <svg class="w-5 h-5" style="color: var(--color-accent1);" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z">
                            </path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">
                            Total Contributed</p>
                        <p class="text-xl md:text-2xl font-bold text-white wrap-break-word"
                            style="font-family: var(--font-header1);">₱{{ number_format($totalCompletedAmount, 2) }}</p>
                    </div>
                </div>
                @if ($nextAmountMilestone)
                    <div class="mt-1">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span style="color: rgba(255,255,255,0.5);">Next:
                                ₱{{ number_format($nextAmountMilestone) }}</span>
                            <span style="color: rgba(255,255,255,0.5);">₱{{ number_format($totalCompletedAmount) }} /
                                ₱{{ number_format($nextAmountMilestone) }}</span>
                        </div>
                        <div class="w-full h-1.5 rounded-full" style="background-color: rgba(255,255,255,0.08);">
                            <div class="h-1.5 rounded-full transition-all duration-500"
                                style="width: {{ min(100, $amountProgress) }}%; background-color: var(--color-accent1);">
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-xs mt-1" style="color: var(--color-accent1);">🏆
                        ₱{{ number_format(end($amountThresholds)) }}+ Contributed</p>
                @endif
            </div>

            <!-- Giving Streak Milestone Card -->
            <div class="rounded-2xl p-5 transition-all duration-300 hover:scale-[1.02] hover:shadow-[0_8px_30px_rgba(0,0,0,0.2)]"
                style="background: linear-gradient(135deg, rgba(59,130,246,0.12), rgba(255,255,255,0.03)); border: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                        style="background-color: rgba(59,130,246,0.2);">
                        <svg class="w-5 h-5" style="color: #3b82f6;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">
                            Giving Streak</p>
                        <p class="text-2xl md:text-3xl font-bold text-white"
                            style="font-family: var(--font-header1);">
                            @if ($firstDonationDate)
                                @php $tenureMonths = $firstDonationDate->diffInMonths(now()); @endphp
                                @if ($tenureMonths < 12)
                                    {{ number_format($tenureMonths, 2) }} {{ Str::plural('Month', $tenureMonths) }}
                                @else
                                    {{ number_format($tenureMonths / 12, 2) }} Years
                                @endif
                            @else
                                —
                            @endif
                        </p>
                    </div>
                </div>
                @if ($firstDonationDate && $tenureDecimal >= 0.1)
                    <div class="mt-1">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span style="color: rgba(255,255,255,0.5);">Since
                                {{ $firstDonationDate->format('M Y') }}</span>
                            @if ($tenureDecimal >= 1)
                                <span
                                    style="color: rgba(255,255,255,0.5);">{{ number_format(floor($tenureDecimal), 0) }}
                                    / {{ $nextTenureMilestone }} Years</span>
                            @endif
                        </div>
                        @if ($tenureDecimal >= 1)
                            <div class="w-full h-1.5 rounded-full" style="background-color: rgba(255,255,255,0.08);">
                                <div class="h-1.5 rounded-full transition-all duration-500"
                                    style="width: {{ min(100, $tenureProgress) }}%; background-color: #3b82f6;"></div>
                            </div>
                        @endif
                    </div>
                @elseif($firstDonationDate)
                    <p class="text-xs" style="color: rgba(255,255,255,0.5);">Since
                        {{ $firstDonationDate->format('M Y') }}</p>
                @else
                    <p class="text-xs" style="color: rgba(255,255,255,0.4);">Not yet started</p>
                @endif
            </div>

            <!-- Active Subscriptions Card -->
            <div class="rounded-2xl p-5 transition-all duration-300 hover:scale-[1.02] hover:shadow-[0_8px_30px_rgba(0,0,0,0.2)]"
                style="background: linear-gradient(135deg, rgba(168,85,247,0.12), rgba(255,255,255,0.03)); border: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                        style="background-color: rgba(168,85,247,0.2);">
                        <svg class="w-5 h-5" style="color: #a855f7;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3">
                            </path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wider"
                            style="color: rgba(255,255,255,0.5);">Active Subscriptions</p>
                        <p class="text-2xl md:text-3xl font-bold text-white"
                            style="font-family: var(--font-header1);">{{ $activeSubscriptions?->count() ?? 0 }}</p>
                    </div>
                </div>
                @php
                    $nextBilling = $activeSubscriptions?->first();
                @endphp
                @if ($nextBilling && $nextBilling->next_billing_date)
                    <p class="text-xs mt-1.5" style="color: rgba(255,255,255,0.5);">
                        Next billing:
                        <span class="font-semibold"
                            style="color: rgba(255,255,255,0.8);">{{ $nextBilling->next_billing_date->format('M d, Y') }}</span>
                        —
                        <span class="font-semibold"
                            style="color: var(--color-accent1);">₱{{ number_format($nextBilling->amount, 2) }}</span>
                    </p>
                @else
                    <p class="text-xs mt-1.5" style="color: rgba(255,255,255,0.4);">No active recurring donations</p>
                @endif
            </div>
        </div>

        <!-- Impact Narrative + Streak + Milestones -->
        <div class="rounded-2xl p-6 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
            style="background: linear-gradient(135deg, rgba(16,185,129,0.08) 0%, rgba(255,255,255,0.02) 100%); border: 1px solid rgba(255,255,255,0.08);">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center"
                    style="background-color: rgba(16,185,129,0.15);">
                    <svg class="w-3.5 h-3.5" style="color: #10b981;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v11.25c0 .414.336.75.75.75h14.25c.414 0 .75-.336.75-.75V6m-15 0h16.5m-16.5 0V3.75A.75.75 0 014.5 3h15a.75.75 0 01.75.75V6">
                        </path>
                    </svg>
                </div>
                <h2 class="text-base font-semibold text-white" style="font-family: var(--font-header1);">Your Impact
                </h2>
            </div>
            <p class="text-sm leading-relaxed" style="color: rgba(255,255,255,0.75);">
                Since <strong style="color: white;">{{ $firstDonationDate?->format('F Y') ?? 'joining' }}</strong>,
                you've made <strong style="color: white;">{{ number_format($totalCompletedCount) }}</strong>
                {{ Str::plural('donation', $totalCompletedCount) }} totaling
                <strong style="color: var(--color-accent1);">₱{{ number_format($totalCompletedAmount, 2) }}</strong>
                across <strong style="color: white;">{{ $programsSupported->count() }}</strong>
                {{ Str::plural('program', $programsSupported->count()) }},
                supporting <strong
                    style="color: white;">{{ number_format($supportedBeneficiaries->count()) }}</strong>
                {{ Str::plural('beneficiary', $supportedBeneficiaries->count()) }}.
            </p>
            @if ($currentStreak >= 2)
                <div class="flex flex-wrap items-center gap-2 mt-4">
                    <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-full"
                        style="background-color: rgba(255,204,51,0.15); color: #fbbf24;">
                        🔥 {{ $currentStreak }}-month giving streak
                    </span>
                </div>
            @endif
        </div>

        <!-- Year-to-Date + Giving by Program Row -->
        <div class="grid gap-5 grid-cols-1 lg:grid-cols-2">
            <!-- Beneficiaries Supported Card -->
            <div class="rounded-2xl p-5 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center"
                        style="background-color: rgba(16,185,129,0.15);">
                        <svg class="w-3.5 h-3.5" style="color: #10b981;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z">
                            </path>
                        </svg>
                    </div>
                    <h2 class="text-base font-semibold text-white" style="font-family: var(--font-header1);">
                        Beneficiaries Supported</h2>
                </div>
                @if ($supportedBeneficiaries->isNotEmpty())
                    <div class="flex flex-wrap gap-2">
                        @foreach ($supportedBeneficiaries->take(12) as $name)
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full"
                                style="background-color: rgba(255,255,255,0.06); color: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,0.08);">
                                {{ $name }}
                            </span>
                        @endforeach
                        @if ($supportedBeneficiaries->count() > 12)
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full"
                                style="background-color: rgba(255,204,51,0.1); color: var(--color-accent1);">
                                + {{ $supportedBeneficiaries->count() - 12 }} more
                            </span>
                        @endif
                    </div>
                @else
                    <p class="text-sm" style="color: rgba(255,255,255,0.4);">No beneficiaries linked yet</p>
                @endif
            </div>

            <!-- Giving by Program Card -->
            <div class="rounded-2xl p-5 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center"
                        style="background-color: rgba(59,130,246,0.15);">
                        <svg class="w-3.5 h-3.5" style="color: #3b82f6;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 3H19.5V9">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                        </svg>
                    </div>
                    <h2 class="text-base font-semibold text-white" style="font-family: var(--font-header1);">Giving by
                        Program</h2>
                </div>
                @if ($givingByProgram->isNotEmpty())
                    @php
                        $maxTotal = $givingByProgram->max('total') ?: 1;
                    @endphp
                    <div class="space-y-3">
                        @foreach ($givingByProgram as $item)
                            @php
                                $pct = ($item['total'] / $maxTotal) * 100;
                                $colors = [
                                    'rgba(16,185,129,0.3)',
                                    'rgba(59,130,246,0.3)',
                                    'rgba(168,85,247,0.3)',
                                    'rgba(255,204,51,0.3)',
                                ];
                                $barColors = [
                                    'rgba(16,185,129,0.8)',
                                    'rgba(59,130,246,0.8)',
                                    'rgba(168,85,247,0.8)',
                                    'rgba(255,204,51,0.8)',
                                ];
                                $idx = $loop->index % 4;
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-medium text-white/70">{{ $item['program_name'] }}</span>
                                    <span
                                        class="text-xs font-semibold text-white">₱{{ number_format($item['total'], 2) }}</span>
                                </div>
                                <div class="w-full h-2 rounded-full"
                                    style="background-color: rgba(255,255,255,0.06);">
                                    <div class="h-2 rounded-full transition-all duration-500"
                                        style="width: {{ $pct }}%; background-color: {{ $barColors[$idx] }};">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm" style="color: rgba(255,255,255,0.4);">No completed donations yet</p>
                @endif
            </div>
        </div>

        <!-- Recent Donations Section -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v11.25c0 .414.336.75.75.75h14.25c.414 0 .75-.336.75-.75V6m-15 0h16.5m-16.5 0V3.75A.75.75 0 014.5 3h15a.75.75 0 01.75.75V6">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Recent
                                Donations</h2>
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Your latest donation
                                activity</p>
                        </div>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full whitespace-nowrap"
                        style="background-color: rgba(255,255,255,0.08); color: rgba(255,255,255,0.6);">
                        Last 10 transactions
                    </span>
                </div>
            </div>
        <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-225">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Transaction
                                </th>
                                <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Date</th>
                                <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Program</th>
                                <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Status</th>
                                <th class="text-right px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Amount</th>
                                <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse (($recentDonations ?? collect()) as $index => $donation)
                                <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}"
                                    style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                    <td class="px-3 sm:px-4 py-3 align-middle">
                                        <div class="font-medium text-white text-xs sm:text-sm"
                                            style="font-family: var(--font-body1);">
                                            {{ $donation->subscription_id ? 'Subscription' : 'One Time Donation' }}
                                        </div>
                                        <div class="text-xs sm:text-xs text-white/40 mt-0.5"
                                            style="font-family: var(--font-body1);">
                                            {{ $donation->donation_type ? ucfirst($donation->donation_type) : 'Financial' }}
                                        </div>
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 align-middle">
                                        <span class="text-xs sm:text-sm whitespace-nowrap"
                                            style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">
                                            {{ $donation->transaction_date?->format('M d, Y') ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 align-middle">
                                        <span class="text-xs sm:text-sm"
                                            style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">
                                            {{ $donation->program?->program_name ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 align-middle text-center">
                                        <span
                                            class="inline-flex px-2 py-0.5 text-xs sm:text-xs font-medium rounded-full whitespace-nowrap"
                                            style="background-color:
                                                  @if ($donation->status === 'completed') rgba(16,185,129,0.15); color: #10b981;
                                                  @elseif($donation->status === 'pending') rgba(245,158,11,0.15); color: #f59e0b;
                                                  @elseif($donation->status === 'failed') rgba(248,113,113,0.15); color: #f87171;
                                                  else rgba(255,255,255,0.1); color: rgba(255,255,255,0.6); @endif">
                                            {{ ucfirst($donation->status) }}
                                        </span>
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 align-middle text-right">
                                        <span class="text-xs sm:text-sm font-semibold text-white whitespace-nowrap"
                                            style="font-family: var(--font-body1);">
                                            ₱{{ number_format((float) $donation->amount, 2) }}
                                        </span>
                                        <div class="text-xs sm:text-xs text-white/40"
                                            style="font-family: var(--font-body1);">
                                            {{ $donation->gateway ? ucfirst($donation->gateway) : 'Manual' }}
                                        </div>
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 align-middle text-center">
                                        @if ($donation->receipts->isNotEmpty() || $donation->receipt_path)
                                            <a href="{{ route('donor.portal.donations.receipt.download', $donation) }}"
                                                class="inline-flex p-1.5 sm:p-2 rounded-lg transition-all hover:bg-white/10 hover:scale-110"
                                                style="color: rgba(255,255,255,0.4);" title="Download Receipt">
                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3">
                                                    </path>
                                                </svg>
                                            </a>
                                        @else
                                            <span class="text-xs" style="color: rgba(255,255,255,0.2);">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center">
                                        <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto mb-3"
                                            style="color: rgba(255,255,255,0.2);" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v11.25c0 .414.336.75.75.75h14.25c.414 0 .75-.336.75-.75V6m-15 0h16.5m-16.5 0V3.75A.75.75 0 014.5 3h15a.75.75 0 01.75.75V6" />
                                        </svg>
                                        <p class="text-sm"
                                            style="color: rgba(255,255,255,0.4); font-family: var(--font-body1);">
                                            No transactions yet.
                                        </p>
                                        <a href="{{ route('donor.portal.donate') }}"
                                            class="inline-flex items-center gap-2 mt-3 px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg text-xs sm:text-sm font-semibold transition-all duration-200 hover:scale-[1.02]"
                                            style="background-color: var(--color-accent1); color: var(--color-primary1); font-family: var(--font-body1);">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            Make Your First Donation
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="text-center pt-4">
            <p class="text-xs" style="color: rgba(255,255,255,0.3);">Thank you for your generosity and support</p>
        </div>
    </div>
</x-dashboardlayout>
