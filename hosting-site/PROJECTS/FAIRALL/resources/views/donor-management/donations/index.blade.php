<x-dashboardlayout name="{{ $name }}" title="Donations">
    <div class="mb-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight" style="font-family: var(--font-header1); color: var(--color-white);">Donations</h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">All donations for {{ $donor->display_name }}</p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3">
                <a href="{{ route('donors.donations.export', [$donor, 'xero']) }}" 
                   class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap"
                   style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.06); color: white; border: 1px solid rgba(255,255,255,0.1);">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"></path>
                    </svg>
                    Export Xero CSV
                </a>
                <a href="{{ route('donors.donations.create', $donor) }}" 
                   class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap"
                   style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                    </svg>
                    Add Donation
                </a>
            </div>
        </div>

        <div class="rounded-2xl overflow-hidden" style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v11.25c0 .414.336.75.75.75h14.25c.414 0 .75-.336.75-.75V6m-15 0h16.5m-16.5 0V3.75A.75.75 0 014.5 3h15a.75.75 0 01.75.75V6"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Donation Records</h2>
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">All donation transactions for this donor</p>
                        </div>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full" style="background-color: rgba(255,255,255,0.08); color: rgba(255,255,255,0.6);">{{ $donations->total() }} total</span>
                </div>
            </div>

            <div class="px-4 sm:px-6 pb-3">
                <x-bulk-actions-bar bulkUrl="{{ route('donors.donations.bulk-destroy', $donor) }}" tableId="donations-{{ $donor->id }}" label="donations" />
            </div>
            <div class="overflow-x-auto">
                <table class="w-full" data-bulk-table="donations-{{ $donor->id }}">
                    <thead>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                            <th class="text-center px-4 py-3">
                                <input type="checkbox" data-select-all class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                            </th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Date</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Contact</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Type</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Checkout Session</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Donation Type</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Amount</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Status</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($donations as $index => $donation)
                            @php
                                $statusColor = '#6b7280';
                                $statusBg = 'rgba(107,114,128,0.15)';
                                if ($donation->status === 'completed') {
                                    $statusColor = '#10b981';
                                    $statusBg = 'rgba(16,185,129,0.15)';
                                } elseif ($donation->status === 'pending') {
                                    $statusColor = '#f59e0b';
                                    $statusBg = 'rgba(245,158,11,0.15)';
                                } elseif ($donation->status === 'failed') {
                                    $statusColor = '#f87171';
                                    $statusBg = 'rgba(248,113,113,0.15)';
                                }
                            @endphp
                            <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" value="{{ $donation->id }}" data-row-checkbox class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                                </td>
                                <td class="px-4 py-3 text-sm" style="color: rgba(255,255,255,0.7);">
                                    {{ optional($donation->transaction_date)->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm" style="color: white;">
                                    <div class="truncate max-w-37.5">{{ $donation->payerEmail() ?? $donation->donor?->display_name ?? '—' }}</div>
                                    @if(data_get($donation->metadata ?? [], 'anonymous'))
                                        <div class="text-xs text-white/40 mt-0.5">Anonymous</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm" style="color: rgba(255,255,255,0.7);">
                                    {{ ucfirst($donation->donation_type ?? $donation->status) }}
                                </td>
                                <td class="px-4 py-3 text-sm" style="color: rgba(255,255,255,0.7);">
                                    <span class="truncate max-w-30 block">{{ $donation->checkout_session_id ?? 'Unlinked' }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm" style="color: rgba(255,255,255,0.7);">
                                    {{ $donation->donation_type ? ucfirst($donation->donation_type) : 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold" style="color: white;">
                                    ₱{{ number_format($donation->amount, 2) }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" 
                                          style="background-color: {{ $statusBg }}; color: {{ $statusColor }};">
                                        {{ ucfirst($donation->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('donors.donations.show', [$donor, $donation]) }}" 
                                           class="p-2 rounded-lg hover:bg-white/10 transition-all" title="View">
                                            <svg class="w-4 h-4" style="color: rgba(255,255,255,0.6);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('donors.donations.edit', [$donor, $donation]) }}" 
                                           class="p-2 rounded-lg hover:bg-white/10 transition-all" title="Edit">
                                            <svg class="w-4 h-4" style="color: rgba(255,255,255,0.6);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                                            </svg>
                                        </a>
                                        <button type="button" class="p-2 rounded-lg hover:bg-red-500/20 transition-all" title="Delete" onclick="document.getElementById('deleteDonationModal_{{ $donation->id }}').showModal()">
                                            <svg class="w-4 h-4" style="color: var(--color-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                                            </svg>
                                        </button>
                                        <x-confirm-dialog id="deleteDonationModal_{{ $donation->id }}"
                                            title="Delete Donation"
                                            message="Are you sure you want to delete donation <strong>{{ $donation->reference_number }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
                                            deleteUrl="{{ route('donors.donations.destroy', [$donor, $donation]) }}" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center">
                                    <p style="color: rgba(255,255,255,0.4);">No donations yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table> </div> 
        </div>

        @if ($donations->hasPages())
                <div>
                    <div class="flex justify-center">
                        {{ $donations->links('pagination.custom') }}
                    </div>
                </div>
            @endif

        <div class="flex justify-end">
            <a href="{{ route('donors.show', $donor) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold transition-all duration-300 hover:bg-white/10"
               style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);"
               onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
               onmouseout="this.style.backgroundColor='transparent'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
                Back to Donor Profile
            </a>
        </div>
    </div>
</x-dashboardlayout>