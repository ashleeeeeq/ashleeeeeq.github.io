<x-dashboardlayout name="{{ $name }}" title="Subscriptions">
    <div class="mb-8 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-white" style="font-family: var(--font-header1);">Subscription Management</h1>
                <p class="text-white/70 mt-2" style="font-family: var(--font-body1);">View and manage your recurring donations.</p>
                <div class="w-16 h-1 mt-3 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        @if (session('status'))
            <div class="rounded-2xl p-4" style="background-color: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.25);">
                <p class="text-sm text-green-200">{{ session('status') }}</p>
            </div>
        @endif

        <div class="rounded-2xl overflow-hidden" style="background-color: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-white/50">Plan</th>
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-white/50">Amount</th>
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-white/50">Status</th>
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-white/50">Next Billing</th>
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-white/50">Reference</th>
                            <th class="px-4 py-3 text-left text-xs uppercase tracking-wider text-white/50">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subscriptions as $subscription)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td class="px-4 py-3 text-sm text-white">{{ $subscription->displayPlanName() }}</td>
                                <td class="px-4 py-3 text-sm text-white/80">{{ number_format((float) $subscription->amount, 2) }} {{ $subscription->currency }}</td>
                                <td class="px-4 py-3 text-sm text-white/80">{{ ucfirst($subscription->status) }}</td>
                                <td class="px-4 py-3 text-sm text-white/80">{{ $subscription->next_billing_date?->format('M d, Y') ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-white/80">{{ $subscription->paypal_subscription_id ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-white/80">
                                    @if (in_array($subscription->status, ['active', 'paused', 'pending'], true))
                                        <button type="button"
                                            onclick="openCancelModal({{ $subscription->id }}, '{{ $subscription->displayPlanName() }}', '{{ number_format((float) $subscription->amount, 2) }}', '{{ $subscription->currency }}', '{{ $subscription->next_billing_date?->format('M d, Y') ?? 'N/A' }}')"
                                            class="px-4 py-2 rounded-xl text-sm font-semibold"
                                            style="background-color: rgba(239,68,68,0.2); color: #fecaca; border: 1px solid rgba(239,68,68,0.35);">
                                            Cancel
                                        </button>
                                    @else
                                        <span class="text-white/50">No actions</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-white/50">No subscriptions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($subscriptions->hasPages())
            <div class="flex justify-end">
                {{ $subscriptions->links('pagination.custom') }}
            </div>
        @endif
    </div>

    <!-- Cancel Confirmation Modal -->
    <dialog id="cancelModal" class="modal">
        <div class="modal-box bg-white text-slate-800 max-w-md">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>

            <h3 class="text-xl font-bold mb-2" style="font-family: var(--font-header1);">Cancel Subscription</h3>
            <p class="text-sm text-gray-500 mb-4">Are you sure you want to cancel this subscription? This action cannot be undone.</p>

            <div class="rounded-xl p-4 mb-6" style="background-color: #f8f8f8; border: 1px solid #e5e5e5;">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Plan</span>
                        <span class="font-medium text-slate-800" id="modal-plan-name"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Amount</span>
                        <span class="font-medium text-slate-800" id="modal-amount"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Next Billing</span>
                        <span class="font-medium text-slate-800" id="modal-next-billing"></span>
                    </div>
                </div>
            </div>

            <div class="rounded-xl p-3 mb-6 text-sm" style="background-color: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); color: #b91c1c;">
                Your recurring donations will stop.
            </div>

            <div class="flex gap-3 justify-between">
                <form method="dialog">
                    <button type="button" onclick="closeCancelModal()" class="px-5 py-2.5 rounded-xl text-sm font-medium border border-gray-300 text-gray-600 hover:bg-gray-50 transition-colors">
                        Keep Subscription
                    </button>
                </form>
                <form id="cancel-form" method="POST" action="">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition-colors" style="background-color: #dc2626; hover:background-color: #b91c1c;">
                        Yes, Cancel Subscription
                    </button>
                </form>
            </div>
        </div>

        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <script>
        var cancelRouteTemplate = '{{ route('donor.portal.subscriptions.cancel', '_ID_') }}';

        function openCancelModal(id, planName, amount, currency, nextBilling) {
            document.getElementById('modal-plan-name').textContent = planName;
            document.getElementById('modal-amount').textContent = amount + ' ' + currency;
            document.getElementById('modal-next-billing').textContent = nextBilling;
            document.getElementById('cancel-form').action = cancelRouteTemplate.replace('_ID_', id);
            document.getElementById('cancelModal').showModal();
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').close();
        }
    </script>
</x-dashboardlayout>
