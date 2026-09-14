@php
    $latestReceipt = $donation->receipts->sortByDesc('id')->first();
    $receiptAvailable = $latestReceipt || $donation->receipt_path;
    $receiptSent = $latestReceipt?->sent_at;
@endphp

<x-dashboardlayout name="{{ $name }}" title="Donation Details">
    <div class="mb-8 space-y-6 space-y-5">
        <!-- Header Banner Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="p-4 sm:p-6 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold tracking-tight wrap-break-word"
                            style="font-family: var(--font-header1); color: var(--color-white);">
                            {{ $donation->gateway_reference ?? ($donation->reference_number ?? ($donation->receipt_number ?? $donation->id)) }}
                        </h1>
                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full whitespace-nowrap"
                            style="background-color:
                                @if ($donation->status === 'completed') rgba(16,185,129,0.15); color: #10b981;
                                @elseif($donation->status === 'pending') rgba(245,158,11,0.15); color: #f59e0b;
                                @elseif($donation->status === 'failed') rgba(248,113,113,0.15); color: #f87171;
                                else rgba(255,204,51,0.15); color: var(--color-accent1); @endif">
                            {{ ucfirst($donation->status ?? '—') }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 mt-3">
                        <svg class="w-4 h-4 shrink-0" style="color: rgba(255,255,255,0.5);" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z">
                            </path>
                        </svg>
                        <p class="text-xs sm:text-sm"
                            style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                            Donor: <span class="font-medium"
                                style="color: rgba(255,255,255,0.85);">{{ $donor->display_name }}</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-2 mt-1">
                        <svg class="w-4 h-4 shrink-0" style="color: rgba(255,255,255,0.5);" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5">
                            </path>
                        </svg>
                        <p class="text-xs sm:text-sm"
                            style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                            Date: <span class="font-medium"
                                style="color: rgba(255,255,255,0.85);">{{ $donation->transaction_date?->format('F d, Y') ?? 'N/A' }}</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-2 mt-1">
                        <svg class="w-4 h-4 shrink-0" style="color: rgba(255,255,255,0.5);" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z">
                            </path>
                        </svg>
                        <p class="text-xs sm:text-sm font-medium"
                            style="color: rgba(255,255,255,0.85); font-family: var(--font-body1);">
                            Amount: {{ number_format((float) $donation->amount, 2) }}
                            {{ $donation->currency ?? config('services.paypal.currency', 'USD') }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 sm:gap-3 mt-2 lg:mt-0">
                    @if ($receiptAvailable)
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl whitespace-nowrap"
                            style="background-color: rgba(16,185,129,0.15); color: #10b981;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Receipt @if ($receiptSent)
                                Sent
                            @else
                                Generated
                            @endif
                        </span>
                    @else
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl whitespace-nowrap"
                            style="background-color: rgba(245,158,11,0.15); color: #f59e0b;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Receipt Pending
                        </span>
                    @endif

                    @if ($donation->gateway === 'manual')
                        <a href="{{ route('donors.donations.edit', [$donor, $donation]) }}"
                            class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-sm sm:text-base font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02]"
                            style="font-family: var(--font-body1); background-color: transparent; color: var(--color-info); border: 1px solid var(--color-info);"
                            onmouseover="this.style.backgroundColor='rgba(59,130,246,0.1)'; this.style.color='var(--color-info-dark)'"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-info)'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10">
                                </path>
                            </svg>
                            Edit
                        </a>
                        <button type="button"
                            class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-sm sm:text-base font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02]"
                            style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                            onmouseover="this.style.backgroundColor='rgba(248,113,113,0.15)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)'"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-danger)'; this.style.borderColor='var(--color-danger)'"
                            onclick="document.getElementById('deleteDonationModal').showModal()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                                </path>
                            </svg>
                            Delete
                        </button>


                        <x-confirm-dialog id="deleteDonationModal" title="Delete Donation"
                            message="Are you sure you want to delete donation <strong>{{ $donation->reference_number }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
                            deleteUrl="{{ route('donors.donations.destroy', [$donor, $donation]) }}" />
                    @endif
                </div>
            </div>
        </div>

        @if (session('status'))
            <div class="rounded-xl p-3 sm:p-4 flex items-start gap-2 sm:gap-3 transition-all duration-300"
                style="background-color: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3);">
                <svg class="w-5 h-5 shrink-0 mt-0.5" style="color: var(--color-success);" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs sm:text-sm"
                    style="color: rgba(255,255,255,0.9); font-family: var(--font-body1);">{{ session('status') }}</span>
            </div>
        @endif

        <!-- Receipt Actions Card -->
        <div class="rounded-2xl p-5 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <h3 class="text-lg font-semibold text-white mb-2" style="font-family: var(--font-header1);">Receipt
                Actions
            </h3>
            <div class="flex gap-2">
                @if ($receiptAvailable)
                    <a href="{{ route('donors.donations.receipt', [$donor, $donation]) }}" target="_blank"
                        class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-sm sm:text-base font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02]"
                        style="font-family: var(--font-body1); background-color: transparent; color: var(--color-info); border: 1px solid var(--color-info);"
                        onmouseover="this.style.backgroundColor='rgba(16,185,129,0.1)'; this.style.color='var(--color-info-dark)'"
                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-info)'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        View
                    </a>
                    <a href="{{ route('donors.donations.receipt.download', [$donor, $donation]) }}"
                        class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-sm sm:text-base font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02]"
                        style="font-family: var(--font-body1); background-color: transparent; color: var(--color-success); border: 1px solid var(--color-success);"
                        onmouseover="this.style.backgroundColor='rgba(16,185,129,0.1)'; this.style.color='var(--color-success-dark)'"
                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-success)'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Download
                    </a>
                @endif

                @if ($donation->donor_id && $donation->status === 'completed')
                    <form method="POST" action="{{ route('donors.donations.resend-receipt', $donation) }}"
                        class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-sm sm:text-base font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02]"
                            style="font-family: var(--font-body1); background-color: transparent; color: var(--color-accent1); border: 1px solid var(--color-accent1);"
                            onmouseover="this.style.backgroundColor='rgba(255,204,51,0.1)'; this.style.color='var(--color-accent1-dark)'"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-accent1)'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182" />
                            </svg>
                            Resend
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid gap-4 sm:gap-5 grid-cols-1 md:grid-cols-2">
            <!-- Donation Information Card -->
            <div class="rounded-2xl p-5 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                        style="background-color: rgba(255,204,51,0.15);">
                        <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white" style="font-family: var(--font-header1);">Donation
                        Information</h3>
                </div>
                <div class="space-y-3 text-sm" style="font-family: var(--font-body1);">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider mb-1"
                            style="color: rgba(255,255,255,0.5);">Donation Type</div>
                        <div class="font-medium" style="color: rgba(255,255,255,0.85);">
                            {{ ucfirst($donation->donation_type ?? 'Financial') }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider mb-1"
                            style="color: rgba(255,255,255,0.5);">Program</div>
                        <div class="font-medium" style="color: rgba(255,255,255,0.85);">
                            {{ $donation->program?->program_name ?? ($donation->program?->name ?? '—') }}</div>
                    </div>
                    @if ($donation->subscription)
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider mb-1"
                                style="color: rgba(255,255,255,0.5);">Subscription Plan</div>
                            <div class="font-medium" style="color: rgba(255,255,255,0.85);">
                                {{ $donation->subscription->displayPlanName() }}</div>
                        </div>
                    @endif
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider mb-1"
                            style="color: rgba(255,255,255,0.5);">Source</div>
                        <div class="font-medium" style="color: rgba(255,255,255,0.85);">
                            {{ ucfirst($donation->gateway ?? 'manual') }}</div>
                    </div>
                </div>
            </div>


            <!-- Metadata Card -->
            <div class="rounded-2xl p-5 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                        style="background-color: rgba(59,130,246,0.15);">
                        <svg class="w-4 h-4" style="color: var(--color-info);" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white" style="font-family: var(--font-header1);">Reference
                        Information</h3>
                </div>
                <div class="space-y-3 text-sm" style="font-family: var(--font-body1);">
                    @if (($donation->gateway ?? 'manual') !== 'manual')
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider mb-1"
                                style="color: rgba(255,255,255,0.5);">Gateway Reference</div>
                            <div class="font-medium" style="color: rgba(255,255,255,0.85);">
                                {{ $donation->gateway_reference ?? '—' }}</div>
                        </div>
                    @endif
                    @if (($donation->gateway ?? 'manual') === 'manual')
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider mb-1"
                                style="color: rgba(255,255,255,0.5);">Reference Number</div>
                            <div class="font-medium" style="color: rgba(255,255,255,0.85);">
                                {{ $donation->reference_number ?? '—' }}</div>
                        </div>
                    @endif
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider mb-1"
                            style="color: rgba(255,255,255,0.5);">Receipt Number</div>
                        <div class="font-medium" style="color: rgba(255,255,255,0.85);">
                            {{ $donation->receipt_number ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider mb-1"
                            style="color: rgba(255,255,255,0.5);">Transaction Date</div>
                        <div class="font-medium" style="color: rgba(255,255,255,0.85);">
                            {{ $donation->transaction_date?->format('F d, Y g:i A') ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider mb-1"
                            style="color: rgba(255,255,255,0.5);">Created At</div>
                        <div class="font-medium" style="color: rgba(255,255,255,0.85);">
                            {{ $donation->created_at?->format('F d, Y g:i A') ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Description Section -->
        @if ($donation->description)
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-6 py-4 border-b"
                    style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Description
                            </h3>
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Additional donation
                                details</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-sm leading-relaxed"
                        style="color: rgba(255,255,255,0.8); font-family: var(--font-body1);">
                        {{ $donation->description }}
                    </p>
                </div>
            </div>
        @endif

        @php
            $sortedAllocs = $donation->allocations->sortBy(fn($a) =>
                ($a->date_allocated?->toDateString() ?? '0000-00-00') . '-' . str_pad((string) $a->id, 10, '0', STR_PAD_LEFT)
            )->values();
            $donationAmount = (float) $donation->amount;
            $runTotal = 0;
            $balMap = [];
            foreach ($sortedAllocs as $s) {
                $runTotal += (int) $s->amount_cents;
                $balMap[$s->id] = $donationAmount - ($runTotal / 100);
            }
        @endphp

        @if ($donation->allocations->count())
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
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
                        <span class="font-semibold text-white text-sm sm:text-base"
                            style="font-family: var(--font-body1);">Allocation Breakdown</span>
                    </div>
                    <div class="text-xs sm:text-sm text-white/50" style="font-family: var(--font-body1);">
                        {{ $donation->allocations->count() }} allocation{{ $donation->allocations->count() !== 1 ? 's' : '' }}
                    </div>
                </div>
                <div class="px-4 sm:px-6 pb-3">
                    <x-bulk-actions-bar bulkUrl="{{ route('funding.allocations.bulk-destroy') }}" tableId="donation-allocations-{{ $donation->id }}" label="allocations" />
                </div>
                <div class="overflow-x-auto" data-bulk-table="donation-allocations-{{ $donation->id }}">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="text-white/60 text-xs uppercase tracking-wider border-b"
                                style="border-color: rgba(255,255,255,0.06); font-family: var(--font-body1);">
                                                                <th class="px-4 py-2 font-medium text-center"><input type="checkbox" data-select-all class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer"></th>
                                                                <th class="px-4 py-2 font-medium">Beneficiary</th>
                                                                <th class="px-4 py-2 font-medium">Amount</th>
                                                                <th class="px-4 py-2 font-medium">Date</th>
                                                                <th class="px-4 py-2 font-medium text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sortedAllocs as $allocation)
                                <tr class="border-b hover:bg-white/5 transition-colors"
                                    style="border-color: rgba(255,255,255,0.04);">
                                    <td class="px-4 py-2 text-center">
                                        <input type="checkbox" value="{{ $allocation->id }}" data-row-checkbox class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                                    </td>
                                    <td class="px-4 py-2 text-white" style="font-family: var(--font-body1);">
                                        {{ $allocation->beneficiary?->full_name ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 text-white font-medium" style="font-family: var(--font-body1);">
                                        ₱{{ number_format($allocation->amount_cents / 100, 2) }}</td>
                                                                    <td class="px-4 py-2 text-white/70" style="font-family: var(--font-body1);">
                                                                        {{ optional($allocation->date_allocated)->format('M d, Y') }}
                                                                    </td>
                                                                    <td class="px-4 py-2">
                                                                        <div class="flex items-center justify-center gap-1 sm:gap-2">
                                                                            <a href="{{ route('funding.allocations.show', $allocation) }}"
                                                                                class="p-1 sm:p-1.5 rounded-lg transition-all hover:bg-white/10 hover:scale-110"
                                                                                title="View">
                                                                                <svg class="w-3.5 h-3.5" style="color: rgba(255,255,255,0.6);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.964 7.182a1.01 1.01 0 0 1 0 .636C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z"></path>
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"></path>
                                                                                </svg>
                                                                            </a>
                                                                            <a href="{{ route('funding.allocations.edit', $allocation) }}"
                                                                                class="p-1 sm:p-1.5 rounded-lg transition-all hover:bg-white/10 hover:scale-110"
                                                                                title="Edit">
                                                                                <svg class="w-3.5 h-3.5" style="color: rgba(255,255,255,0.6);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                                                                                </svg>
                                                                            </a>
                                                                            <button type="button"
                                                                                class="p-1 sm:p-1.5 rounded-lg transition-all hover:bg-red-500/20 hover:scale-110"
                                                                                title="Delete"
                                                                                onclick="document.getElementById('deleteAllocModal_{{ $allocation->id }}').showModal()">
                                                                                <svg class="w-3.5 h-3.5" style="color: var(--color-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                                                                                </svg>
                                                                            </button>
                                                                            <x-confirm-dialog id="deleteAllocModal_{{ $allocation->id }}"
                                                                                title="Delete Allocation"
                                                                                message="Are you sure you want to delete this allocation for <strong>{{ $allocation->beneficiary?->display_name ?? 'beneficiary' }}</strong> of <strong>₱{{ number_format($allocation->amount_cents / 100, 2) }}</strong>? This action cannot be undone."
                                                                                deleteUrl="{{ route('funding.allocations.destroy', $allocation) }}" />
                                                                        </div>
                                                                    </td>
                                                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @php
                    $totalAllocCents = (int) $donation->allocations->sum('amount_cents');
                    $totalAllocated = $totalAllocCents / 100;
                    $finalBalance = (float) $donation->amount - $totalAllocated;
                @endphp
                <div class="px-4 sm:px-6 py-3 flex items-center justify-between gap-4 flex-wrap text-sm"
                    style="border-top: 1px solid rgba(255,255,255,0.06); background-color: rgba(255,255,255,0.02);">
                    <span style="font-family: var(--font-body1);">
                        <span class="text-white/60">Total Allocated:</span>
                        <span class="text-white font-semibold ml-1">₱{{ number_format($totalAllocated, 2) }}</span>
                    </span>
                    <span style="font-family: var(--font-body1);">
                        <span class="text-white/60">Balance:</span>
                        <span class="{{ $finalBalance > 0 ? 'text-green-400' : 'text-white/50' }} font-semibold ml-1">
                            ₱{{ number_format(max(0, $finalBalance), 2) }}
                        </span>
                    </span>
                </div>
            </div>
        @endif

        <!-- Back Button -->
        <div class="flex justify-end pt-4">
            <a href="{{ route('donors.donations.all') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold transition-all duration-300 hover:bg-white/10"
                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);"
                onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
                onmouseout="this.style.backgroundColor='transparent'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
                Back to All Donations
            </a>
        </div>
    </div>
</x-dashboardlayout>
