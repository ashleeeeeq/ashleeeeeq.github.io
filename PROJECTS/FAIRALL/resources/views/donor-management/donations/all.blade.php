<x-dashboardlayout name="{{ $name }}" title="All Donations">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight" style="font-family: var(--font-header1); color: var(--color-white);">All Donations</h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">Ledger of all confirmed and manual donations. Use filters to narrow results.</p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full sm:w-auto">
                <a href="{{ route('donors.donations.create-all') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-semibold transition-all duration-200 hover:scale-[1.02] w-full sm:w-auto"
                   style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                    </svg>
                    Add Donation
                </a>
                <a href="{{ route('donors.donations.export-all', array_merge(request()->except('page'), ['format' => 'xero'])) }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-semibold transition-all duration-200 hover:scale-[1.02] w-full sm:w-auto"
                   style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.06); color: white; border: 1px solid rgba(255,255,255,0.1);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"></path>
                    </svg>
                    Export Xero CSV
                </a>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-1">
            <a href="{{ route('donors.donations.all', request()->except(['page', 'linked', 'anonymous'])) }}"
               class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 whitespace-nowrap"
               style="{{ !request()->has('linked') && !request()->has('anonymous') ? 'background-color: var(--color-accent1); color: var(--color-primary1);' : 'background-color: rgba(255,255,255,0.05); color: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,0.08);' }}">
                All
            </a>
            <a href="{{ route('donors.donations.all', array_merge(request()->except(['page', 'anonymous', 'linked']), ['linked' => 1])) }}"
               class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 whitespace-nowrap"
               style="{{ request()->boolean('linked') ? 'background-color: var(--color-accent1); color: var(--color-primary1);' : 'background-color: rgba(255,255,255,0.05); color: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,0.08);' }}">
                Linked
            </a>
            <a href="{{ route('donors.donations.all', array_merge(request()->except(['page', 'anonymous', 'linked']), ['anonymous' => 1])) }}"
               class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 whitespace-nowrap"
               style="{{ request()->boolean('anonymous') ? 'background-color: var(--color-accent1); color: var(--color-primary1);' : 'background-color: rgba(255,255,255,0.05); color: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,0.08);' }}">
                Anonymous
            </a>
        </div>

        <!-- Filter Bar -->
        <div class="rounded-2xl mb-6 p-4" style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
            <form method="GET" action="" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <div>
                    <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Donor</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-primary1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        <input type="text" name="donor" placeholder="Search donor..." value="{{ request('donor') }}"
                               class="w-full rounded-xl pl-10 pr-4 py-2 text-sm text-primary1"
                               style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                    </div>
                </div>
                <div>
                    <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Program</label>
                    <select name="program_id" class="w-full rounded-xl px-4 py-2 text-sm"
                            style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">
                        <option value="" style="background: var(--color-white); color: var(--color-primary1);">All Programs</option>
                        <option value="no_programs" {{ request('program_id') === 'no_programs' ? 'selected' : '' }} style="background: var(--color-white); color: var(--color-primary1);">No Program</option>
                        @foreach ($programs as $program)
                            <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }} style="background: var(--color-white); color: var(--color-primary1);">
                                {{ $program->program_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Gateway</label>
                    <select name="gateway" class="w-full rounded-xl px-4 py-2 text-sm"
                            style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">
                        <option value="" style="background: var(--color-white); color: var(--color-primary1);">Any Gateway</option>
                        <option value="paypal" {{ request('gateway') == 'paypal' ? 'selected' : '' }} style="background: var(--color-white); color: var(--color-primary1);">PayPal</option>
                        <option value="xendit" {{ request('gateway') == 'xendit' ? 'selected' : '' }} style="background: var(--color-white); color: var(--color-primary1);">Xendit</option>
                        <option value="manual" {{ request('gateway') == 'manual' ? 'selected' : '' }} style="background: var(--color-white); color: var(--color-primary1);">Manual</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Status</label>
                    <select name="status" class="w-full rounded-xl px-4 py-2 text-sm"
                            style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">
                        <option value="" style="background: var(--color-white); color: var(--color-primary1);">Any Status</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }} style="background: var(--color-white); color: var(--color-primary1);">Completed</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }} style="background: var(--color-white); color: var(--color-primary1);">Pending</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }} style="background: var(--color-white); color: var(--color-primary1);">Failed</option>
                    </select>
                </div>
                @if (request()->boolean('linked'))
                    <input type="hidden" name="linked" value="1">
                @endif
                @if (request()->boolean('anonymous'))
                    <input type="hidden" name="anonymous" value="1">
                @endif
                <div class="flex gap-2 sm:col-span-2 lg:col-span-1">
                    <button type="submit"
                            class="flex-1 px-5 py-2 text-sm font-semibold rounded-xl transition-all duration-200 hover:scale-[1.02]"
                            style="background-color: var(--color-accent1); color: var(--color-primary1);">
                        Filter
                    </button>
                    <a href="{{ route('donors.donations.all') }}"
                       class="flex-1 px-5 py-2 text-sm font-semibold rounded-xl transition-all duration-200 text-center"
                       style="background-color: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1);">
                        Clear
                    </a>
                </div>
            </form>
        </div>


        <!-- Donations Table -->
        <div class="rounded-2xl overflow-hidden" style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-4 sm:px-6 pb-3 pt-3">
                <x-bulk-actions-bar bulkUrl="{{ route('donors.donations.bulk-destroy-all') }}" tableId="donationsAll" label="donations" />
            </div>
            <div class="overflow-x-auto">
                <table class="w-full" data-bulk-table="donationsAll">
                    <thead>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                            <th class="text-center px-4 py-3">
                                <input type="checkbox" data-select-all class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                            </th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Date</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Source</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Ref #</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Donor</th>
                             <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Amount</th>
                             <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Alloc. Rate</th>
                             <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Program</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Actions</th>
                        <tr>
                    </thead>
                    <tbody>
                        @forelse ($donations as $index => $donation)
                            <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" value="{{ $donation->id }}" data-row-checkbox class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                                </td>
                                <td class="px-4 py-3 text-sm" style="color: rgba(255,255,255,0.7);">
                                    {{ $donation->transaction_date?->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full"
                                          style="background-color: rgba(255,204,51,0.15); color: var(--color-accent1);">
                                        {{ ucfirst($donation->gateway ?? 'manual') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm font-medium" style="color: white;">
                                    {{ $donation->gateway_reference ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($donation->donor)
                                        <a href="{{ route('donors.show', $donation->donor) }}"
                                           class="text-sm font-medium hover:text-accent1 transition-colors duration-200"
                                            style="color: white; font-family: var(--font-body1);"
                                            onmouseover="this.style.color='var(--color-accent1)'"
                                            onmouseout="this.style.color='white'">
                                            {{ $donation->donor->display_name }}
                                        </a>
                                    @else
                                        <span style="color: rgba(255,255,255,0.4);">Anonymous</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold" style="color: white;">
                                    ₱{{ number_format((float) $donation->amount, 2) }}
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $allocSum = (int) ($donation->allocations_sum_amount_cents ?? 0);
                                        $rate = (float) $donation->amount > 0 ? min(100, round(($allocSum / 100 / (float) $donation->amount) * 100)) : 0;
                                    @endphp
                                    <div class="flex items-center gap-2 max-w-[120px]">
                                        <div class="flex-1 h-2 rounded-full overflow-hidden" style="background-color: rgba(255,255,255,0.08);">
                                            <div class="h-full rounded-full" style="width: {{ $rate }}%; background-color: {{ $rate === 100 ? 'var(--color-success)' : ($rate > 0 ? 'var(--color-accent1)' : 'rgba(255,255,255,0.2)') }};"></div>
                                        </div>
                                        <span class="text-xs font-semibold whitespace-nowrap {{ $rate === 100 ? 'text-green-400' : 'text-white/70' }}">
                                            {{ $rate }}%
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm" style="color: rgba(255,255,255,0.7);">
                                    {{ $donation->program?->program_name ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        @if ($donation->donor)
                                            <a href="{{ route('donors.donations.show', [$donation->donor, $donation]) }}"
                                               class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-white/10 transition-all">
                                                <svg class="w-4 h-4" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </a>
                                        @endif
                                        @if ($donation->gateway === 'manual')
                                            <a href="{{ route('donors.donations.edit-all', $donation) }}"
                                               class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-white/10 transition-all">
                                                <svg class="w-4 h-4" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                                                </svg>
                                            </a>
                                            <button type="button"
                                               class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-red-500/20 transition-all"
                                               title="Delete"
                                               onclick="document.getElementById('deleteDonationModal_{{ $donation->id }}').showModal()">
                                                <svg class="w-4 h-4" style="color: var(--color-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                                                </svg>
                                            </button>
                                            <x-confirm-dialog id="deleteDonationModal_{{ $donation->id }}"
                                                title="Delete Donation"
                                                message="Are you sure you want to delete donation <strong>{{ $donation->reference_number }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
                                                deleteUrl="{{ route('donors.donations.destroy-all', $donation) }}" />
                                        @endif
                                        @if (is_null($donation->donor))
                                            <form method="POST" action="{{ route('donors.donations.reconcile', $donation) }}" class="flex items-center gap-1">
                                                @csrf
                                                @method('PATCH')
                                                <select name="donor_id" data-choices="true"
                                                        class="link-donor-choices rounded-lg px-2 py-1.5 text-xs">
                                                    <option value="">Link donor</option>
                                                    @foreach ($donors as $d)
                                                        <option value="{{ $d->id }}">{{ $d->display_name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit"
                                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold"
                                                        style="background-color: var(--color-accent1); color: var(--color-primary1);">
                                                    Link
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center">
                                    <p style="color: rgba(255,255,255,0.4);">No donations found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


        @if ($donations->hasPages())
            <div class="flex justify-center mt-6">
                {{ $donations->links('pagination.custom') }}
            </div>
        @endif


       
        <!-- Back Button -->
        <div class="flex justify-end pt-4">
            <a href="{{ route('donors.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold transition-all duration-300 hover:bg-white/10"
               style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);"
               onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
               onmouseout="this.style.backgroundColor='transparent'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
                Back to Donor Management
            </a>
        </div>
    </div>

    <style>
        .choices:has(select.link-donor-choices) .choices__inner {
            min-height: 0;
            padding: 2px 8px;
            font-size: 0.75rem;
        }
        .choices:has(select.link-donor-choices) .choices__list--single {
            padding: 0;
        }
    </style>
</x-dashboardlayout>

