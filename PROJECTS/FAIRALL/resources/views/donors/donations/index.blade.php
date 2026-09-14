<x-dashboardlayout name="{{ $name }}" title="Donation History">
    <div class="mb-8 space-y-6 space-y-6">
        <!-- Header Section -->
        <div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight" style="font-family: var(--font-header1); color: var(--color-white);">Donation History</h1>
            <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">Review donations, manual entries, and downloadable receipts.</p>
            <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="{{ route('donate.page') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-[1.02]"
                   style="background-color: var(--color-accent1); color: var(--color-primary1);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                    </svg>
                    New Donation
                </a>
                <a href="{{ route('donor.portal.subscriptions.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-[1.02]"
                   style="background-color: rgba(255,255,255,0.06); color: white; border: 1px solid rgba(255,255,255,0.1);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3"></path>
                    </svg>
                    Manage Subscriptions
                </a>
            </div>
        </div>


        <!-- Donations Table Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v7.5m2.25-6.466a9.016 9.016 0 0 0-3.461-.203c-.536.072-.974.478-1.021 1.017a4.559 4.559 0 0 0-.018.402c0 .464.336.844.775.994l2.95 1.012c.44.15.775.53.775.994 0 .136-.006.27-.018.402-.047.539-.485.945-1.021 1.017a9.077 9.077 0 0 1-3.461-.203M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Donation Records</h2>
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">All your donation transactions</p>
                        </div>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full" style="background-color: rgba(255,255,255,0.08); color: rgba(255,255,255,0.6);">{{ $donations->total() }} donations</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Reference</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Gateway</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Donation Type</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Date</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Amount</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($donations as $index => $donation)
                            <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                <td class="px-4 py-3 text-sm font-medium" style="color: white;">
                                    {{ $donation->gateway_reference ?? $donation->reference_number ?? $donation->receipt_number ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full"
                                          style="background-color: rgba(255,204,51,0.15); color: var(--color-accent1);">
                                        {{ ucfirst($donation->gateway) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full"
                                          style="background-color:
                                              @if($donation->status === 'completed') rgba(16,185,129,0.15); color: #10b981;
                                              @elseif($donation->status === 'pending') rgba(245,158,11,0.15); color: #f59e0b;
                                              @elseif($donation->status === 'failed') rgba(248,113,113,0.15); color: #f87171;
                                              else rgba(255,255,255,0.1); color: rgba(255,255,255,0.6);
                                              @endif">
                                        {{ ucfirst($donation->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm" style="color: rgba(255,255,255,0.7);">
                                    {{ $donation->donation_type ? ucfirst($donation->donation_type) : 'Not specified' }}
                                </td>
                                <td class="px-4 py-3 text-sm" style="color: rgba(255,255,255,0.7);">
                                    {{ $donation->transaction_date?->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold" style="color: white;">
                                    ₱{{ number_format((float) $donation->amount, 2) }}
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $receiptAvailable = $donation->receipts->isNotEmpty() || filled($donation->receipt_path);
                                    @endphp


                                    @if ($receiptAvailable)
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('donor.portal.donations.receipt', $donation) }}" target="_blank" rel="noopener noreferrer"
                                               class="w-8 h-8 rounded-lg flex items-center justify-center transition-all hover:bg-white/10"
                                               title="View Receipt">
                                                <svg class="w-4 h-4" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('donor.portal.donations.receipt.download', $donation) }}"
                                               class="w-8 h-8 rounded-lg flex items-center justify-center transition-all hover:bg-white/10"
                                               title="Download Receipt">
                                                <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-xs" style="color: rgba(255,255,255,0.4);">Receipt pending</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.15);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v11.25c0 .414.336.75.75.75h14.25c.414 0 .75-.336.75-.75V6m-15 0h16.5m-16.5 0V3.75A.75.75 0 014.5 3h15a.75.75 0 01.75.75V6"></path>
                                    </svg>
                                    <p class="text-sm" style="color: rgba(255,255,255,0.4);">No donations yet.</p>
                                    <a href="{{ route('donate.page') }}"
                                       class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 hover:scale-[1.02]"
                                       style="background-color: var(--color-accent1); color: var(--color-primary1);">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                                        </svg>
                                        Make a Donation
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
           
            <!-- Pagination inside card footer -->
            @if ($donations->hasPages())
                <div class="px-6 py-4 border-t" style="border-color: rgba(255,255,255,0.08);">
                    <div class="flex justify-center">
                        {{ $donations->links('pagination.custom') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-dashboardlayout>


<!-- Success Modal -->
@if (session('status'))
    <div id="status-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div class="relative z-10 w-full max-w-md rounded-2xl transform scale-95 transition-all duration-300"
             style="background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(255,255,255,0.03)); border: 1px solid rgba(16,185,129,0.3); backdrop-filter: blur(10px);">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full" style="background-color: rgba(16,185,129,0.15);">
                        <svg class="w-6 h-6" style="color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: #10b981;">Donation Complete</p>
                        <p class="mt-2 text-sm leading-relaxed" style="color: rgba(255,255,255,0.8);">{{ session('status') }}</p>
                    </div>
                    <button id="status-modal-close" class="rounded-full p-2 transition-colors hover:bg-white/10" aria-label="Close modal" style="color: rgba(255,255,255,0.5);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modal = document.getElementById('status-modal');
            var closeButton = document.getElementById('status-modal-close');
            if (! modal) return;


            requestAnimationFrame(function () {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                modal.classList.add('opacity-100');
                modal.querySelector('.relative')?.classList.remove('scale-95');
            });


            var dismiss = function () {
                modal.classList.add('opacity-0', 'pointer-events-none');
                modal.querySelector('.relative')?.classList.add('scale-95');
                setTimeout(function () { modal.remove(); }, 300);
            };


            closeButton?.addEventListener('click', dismiss);
            modal.addEventListener('click', function (event) {
                if (event.target === modal || event.target.classList.contains('absolute')) {
                    dismiss();
                }
            });
            setTimeout(dismiss, 6000);
        });
    </script>
@endif

