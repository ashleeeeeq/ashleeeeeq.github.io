<x-dashboardlayout title="Allocate Funds">
    <div class="mb-8 space-y-6 space-y-6">
        <!-- Header Section -->
        <div>
            <a href="{{ route('funding.allocations.index') }}"
                class="text-sm text-white/60 hover:text-white transition-colors inline-flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Back to allocations
            </a>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight"
                style="font-family: var(--font-header1); color: var(--color-white);">Allocate Funds</h1>
            <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">Assign donation(s) or grant(s) to beneficiaries — add multiple allocations for the same source</p>
            <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
        </div>

        @include('funding._tabs')

        <form action="{{ route('funding.allocations.store') }}" method="POST" class="space-y-6" id="allocations-form">
            @csrf
            <input type="hidden" name="return_url" value="{{ old('return_url', session('return_url')) }}">

            <!-- Source Selection Card -->
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
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 16.5v.75m3-3v3M15 12v5.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Allocation Source</h2>
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Selected source applies to all rows below</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    <!-- Program Filter -->
                    <div>
                        <label class="block mb-2 text-xs font-semibold uppercase tracking-wider"
                            style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Program</label>
                        <div class="relative">
                            <select name="program_filter" id="program_filter"
                                class="w-full rounded-xl px-4 py-2.5 appearance-none transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1 cursor-pointer"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">
                                <option value="" style="background-color: var(--color-primary1); color: white;">No Program</option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->id }}"
                                        style="background-color: white; color: var(--color-primary1);"
                                        @selected(old('program_filter', $programFilter) == $program->id)>
                                        {{ $program->program_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Allocation Source Radio -->
                    <div>
                        <label class="block mb-2 text-xs font-semibold uppercase tracking-wider"
                            style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Allocation Source</label>
                        <div class="flex flex-wrap gap-4">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="allocation_source" value="donation"
                                    class="w-4 h-4 transition-all duration-200"
                                    style="accent-color: var(--color-accent1);"
                                    {{ old('allocation_source', $sourceType) === 'donation' ? 'checked' : '' }}>
                                <span class="text-sm text-white/80">Donation</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="allocation_source" value="grant"
                                    class="w-4 h-4 transition-all duration-200"
                                    style="accent-color: var(--color-accent1);"
                                    {{ old('allocation_source', $sourceType) === 'grant' ? 'checked' : '' }}>
                                <span class="text-sm text-white/80">Grant</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <!-- Donation Field -->
                        <div id="donation-field">
                            <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Donation</label>
                            <div class="relative">
                                <select name="donation_id" id="donation_id" data-choices="true"
                                    class="w-full rounded-xl px-4 py-2.5 appearance-none transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1 cursor-pointer"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">
                                    <option value="" style="background-color: var(--color-primary1); color: white;">Select a donation</option>
                                    @foreach ($donations as $donation)
                                        <option value="{{ $donation->id }}"
                                            style="background-color: white; color: var(--color-primary1);"
                                            @selected(old('donation_id', $donationId) == $donation->id)
                                            data-available-cents="{{ $donation->available_amount_cents }}"
                                            data-total-cents="{{ $donation->amount_cents }}"
                                            data-allocated-cents="{{ $donation->allocated_amount_cents }}"
                                            data-program-id="{{ $donation->program_id ?? '' }}">
                                            {{ optional($donation->donor)->display_name ?? 'No donor' }} -
                                            ₱{{ number_format((float) $donation->amount, 2) }} (available
                                            ₱{{ number_format($donation->available_amount_cents / 100, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Grant Field -->
                        <div id="grant-field" style="display: none;">
                            <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Grant</label>
                            <div class="relative">
                                <select name="grant_id" id="grant_id" data-choices="true"
                                    class="w-full rounded-xl px-4 py-2.5 appearance-none transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1 cursor-pointer"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">
                                    <option value="" style="background-color: var(--color-primary1); color: white;">Select a grant</option>
                                    @foreach ($grants as $grant)
                                        <option value="{{ $grant->id }}"
                                            style="background-color: white; color: var(--color-primary1);"
                                            @selected(old('grant_id', $grantId) == $grant->id)
                                            data-available-cents="{{ $grant->available_amount_cents }}"
                                            data-total-cents="{{ $grant->amount_cents }}"
                                            data-allocated-cents="{{ $grant->allocated_amount_cents }}"
                                            data-program-ids="{{ $grant->programs->pluck('id')->implode(',') }}">
                                            #{{ $grant->id }} - {{ $grant->grant_name }} -
                                            ₱{{ number_format((float) $grant->total_amount, 2) }} (available
                                            ₱{{ number_format($grant->available_amount_cents / 100, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Allowable Note -->
                    <div>
                        <div id="allocation-allowable-note" class="rounded-xl p-4 text-sm" style="background-color: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.06); color: white;">
                            Select a donation or grant to see the allowable amount to allocate.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Allocations Rows -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-white font-semibold" style="font-family: var(--font-header1);">Allocations</h3>
                    <span class="text-xs" style="color: rgba(255,255,255,0.5);">Each row is one allocation to a beneficiary</span>
                </div>
                <div id="allocations-rows" class="space-y-4"></div>
                <div class="flex justify-start">
                    <button type="button" id="add-allocation"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02]"
                        style="font-family: var(--font-body1); background-color: transparent; color: var(--color-accent1); border: 1px solid var(--color-accent1);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Allocation
                    </button>
                </div>
                <x-forms.errors name="allocations" />
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4">
                <a href="{{ route('funding.allocations.index') }}"
                    class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] text-center inline-flex items-center justify-center gap-2 group"
                    style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);">
                    <svg class="w-4 h-4 transition-all duration-300 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center justify-center gap-2 group"
                    style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                    <svg class="w-4 h-4 transition-all duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                    Save Allocation(s)
                </button>
            </div>
        </form>
    </div>

    <template id="allocation-template">
        <div class="allocation-card rounded-2xl overflow-hidden" style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-6 py-3 border-b flex items-center justify-between" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <h4 class="font-semibold text-white allocation-card-title" style="font-family: var(--font-header1);">Allocation #__NUMBER__</h4>
                <button type="button" class="remove-allocation inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-lg transition-all duration-200" style="font-family: var(--font-body1); color: var(--color-danger); background-color: rgba(248,113,113,0.12); border: 1px solid rgba(248,113,113,0.2);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Remove
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Beneficiary <span style="color: var(--color-danger);">*</span></label>
                        <div class="relative">
                            <select name="allocations[__INDEX__][beneficiary_id]" class="allocation-beneficiary w-full rounded-xl px-4 py-2.5 appearance-none transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1 cursor-pointer" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;" required>
                                <option value="" style="background-color: var(--color-primary1); color: white;">Select a beneficiary</option>
                                @foreach ($beneficiaries as $b)
                                    <option value="{{ $b->id }}" style="background-color: white; color: var(--color-primary1);" data-program-ids="{{ $b->programs->pluck('id')->implode(',') }}">{{ $b->display_name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"><svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path></svg></div>
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Amount <span style="color: var(--color-danger);">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-primary1">₱</span>
                            <input type="number" step="0.01" name="allocations[__INDEX__][amount]" class="allocation-amount w-full rounded-xl pl-8 pr-4 py-2.5 text-primary1 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);" placeholder="0.00" required>
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Allocation Date <span style="color: var(--color-danger);">*</span></label>
                        <input type="date" name="allocations[__INDEX__][date_allocated]" class="allocation-date w-full rounded-xl px-4 py-2.5 text-primary1 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Notes</label>
                        <textarea name="allocations[__INDEX__][notes]" rows="2" class="allocation-notes w-full rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1 resize-y" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;" placeholder="Optional notes"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <script>
        (function() {
            const radios = document.querySelectorAll('input[name="allocation_source"]');
            const donationField = document.getElementById('donation-field');
            const grantField = document.getElementById('grant-field');
            const allocationNote = document.getElementById('allocation-allowable-note');
            const donationSelect = document.getElementById('donation_id');
            const grantSelect = document.getElementById('grant_id');
            const programFilter = document.getElementById('program_filter');
            const rows = document.getElementById('allocations-rows');
            const template = document.getElementById('allocation-template').innerHTML;
            const addBtn = document.getElementById('add-allocation');
            const existingAllocations = @json(old('allocations', []));

            function formatCurrency(cents) { return '₱' + (Number(cents || 0) / 100).toFixed(2); }
            function selectedOption(select) { return select?.options?.[select.selectedIndex] ?? null; }
            function todayISO() { return new Date().toISOString().split('T')[0]; }

            function captureOriginal(select) {
                if (!select || select._originalOptions) return;
                select._originalOptions = Array.from(select.options).map(o => ({
                    value: o.value,
                    label: o.textContent.trim(),
                    selected: o.selected,
                    disabled: o.disabled,
                    customProperties: {
                        programId: o.dataset.programId || '',
                        programIds: o.dataset.programIds || '',
                        availableCents: o.dataset.availableCents || '',
                        totalCents: o.dataset.totalCents || '',
                        allocatedCents: o.dataset.allocatedCents || ''
                    }
                }));
            }

            function refreshChoices(select, filteredOptions) {
                const choices = select._choicesInstance;
                if (!choices) {
                    const cur = select.value;
                    // Preserve placeholder (first option with empty value) separately if needed but rebuild fully
                    select.innerHTML = '';
                    filteredOptions.forEach(o => {
                        const opt = document.createElement('option');
                        opt.value = o.value;
                        opt.textContent = o.label;
                        opt.selected = o.value !== '' && o.value === cur;
                        opt.disabled = o.disabled;
                        if (o.value === '') {
                            opt.style.backgroundColor = 'var(--color-primary1)';
                            opt.style.color = 'white';
                        } else {
                            opt.style.backgroundColor = 'white';
                            opt.style.color = 'var(--color-primary1)';
                        }
                        Object.entries(o.customProperties || {}).forEach(([k, v]) => {
                            if (v !== undefined && v !== null && v !== '') opt.dataset[k] = v;
                        });
                        select.appendChild(opt);
                    });
                    // Restore placeholder selection if cur was empty or not found
                    if (!filteredOptions.find(o => o.value === cur)) select.value = filteredOptions[0]?.value || '';
                    else select.value = cur;
                    return;
                }
                const cur = select.value;
                choices.clearStore();
                choices.setChoices(filteredOptions.map(o => ({
                    value: o.value,
                    label: o.label,
                    selected: o.value === cur,
                    disabled: o.disabled,
                    customProperties: o.customProperties
                })), 'value', 'label', true);
                Array.from(select.options).forEach(opt => {
                    const orig = filteredOptions.find(o => o.value === opt.value);
                    if (orig?.customProperties) {
                        Object.entries(orig.customProperties).forEach(([k, v]) => {
                            if (v !== undefined && v !== null && v !== '') opt.dataset[k] = v;
                        });
                    }
                });
                // Re-apply data-* after setChoices drops them
                if (!filteredOptions.find(o => o.value === cur)) select.value = '';
            }

            // Snapshot for template beneficiaries (used for new rows)
            const templateBeneficiaryOptions = (() => {
                const tmp = document.createElement('div');
                tmp.innerHTML = template;
                const sel = tmp.querySelector('.allocation-beneficiary');
                if (!sel) return [];
                return Array.from(sel.options).map(o => ({
                    value: o.value,
                    label: o.textContent.trim(),
                    selected: false,
                    disabled: o.disabled,
                    customProperties: {
                        programIds: o.dataset.programIds || ''
                    }
                }));
            })();

            captureOriginal(donationSelect);
            captureOriginal(grantSelect);

            function syncAllowance() {
                const selected = document.querySelector('input[name="allocation_source"]:checked')?.value || 'donation';
                const option = selected === 'donation' ? selectedOption(donationSelect) : selectedOption(grantSelect);
                const available = option?.dataset?.availableCents;
                const allocated = option?.dataset?.allocatedCents;
                const total = option?.dataset?.totalCents;
                if (!option || !option.value || !available) {
                    allocationNote.innerHTML = 'Select a donation or grant to see the allowable amount to allocate.';
                    allocationNote.style.backgroundColor = 'rgba(255,255,255,0.04)';
                    allocationNote.style.borderColor = 'rgba(255,255,255,0.06)';
                    return;
                }
                allocationNote.innerHTML = `<strong>Allowable amount to allocate:</strong> ${formatCurrency(available)}<br><span style="color: rgba(255,255,255,0.6);">Allocated so far: ${formatCurrency(allocated)} of ${formatCurrency(total)}</span>`;
                allocationNote.style.backgroundColor = 'rgba(16,185,129,0.08)';
                allocationNote.style.borderColor = 'rgba(16,185,129,0.3)';
            }

            function syncSourceFields() {
                const selected = document.querySelector('input[name="allocation_source"]:checked')?.value || 'donation';
                donationField.style.display = selected === 'donation' ? 'block' : 'none';
                grantField.style.display = selected === 'grant' ? 'block' : 'none';
                syncAllowance();
            }

            function filterByProgram() {
                const pid = programFilter ? programFilter.value : '';
                // Donation: No Program => only unlinked (programId === ""), otherwise exact match
                // Preserve currently selected option even if program mismatches so pre-selected source via ?donation_id remains visible on show-all
                if (donationSelect && donationSelect._originalOptions) {
                    const curDonation = donationSelect.value;
                    const filtered = donationSelect._originalOptions.filter(o => {
                        if (!o.value) return true;
                        if (o.value !== '' && o.value === curDonation) return true;
                        const prog = (o.customProperties.programId || '').trim();
                        if (pid === '') return prog === '';
                        return pid === prog;
                    });
                    refreshChoices(donationSelect, filtered);
                }
                // Grant: No Program => only grants with zero programs, otherwise includes
                // Preserve selected grant for show-all (program_filter='') so grant allocation button stays selected
                if (grantSelect && grantSelect._originalOptions) {
                    const curGrant = grantSelect.value;
                    const filtered = grantSelect._originalOptions.filter(o => {
                        if (!o.value) return true;
                        if (o.value !== '' && o.value === curGrant) return true;
                        const raw = (o.customProperties.programIds || '').trim();
                        const ids = raw ? raw.split(',').filter(Boolean) : [];
                        if (pid === '') return ids.length === 0;
                        return ids.includes(pid);
                    });
                    refreshChoices(grantSelect, filtered);
                }
                // Beneficiary per-row: No Program => any beneficiary (unlinked source accepts any)
                rows.querySelectorAll('.allocation-beneficiary').forEach(sel => {
                    if (!sel._originalOptions) sel._originalOptions = templateBeneficiaryOptions;
                    const filtered = sel._originalOptions.filter(o => {
                        if (!o.value) return true;
                        const ids = (o.customProperties.programIds || '').split(',').filter(Boolean);
                        return pid === '' || ids.includes(pid);
                    });
                    refreshChoices(sel, filtered);
                    if (sel.value && !filtered.find(o => o.value === sel.value)) sel.value = '';
                });
                syncAllowance();
            }

            function reindex() {
                rows.querySelectorAll('.allocation-card').forEach((card, idx) => {
                    card.querySelector('.allocation-card-title').textContent = `Allocation #${idx + 1}`;
                    card.querySelectorAll('input, select, textarea').forEach(el => {
                        if (el.name) el.name = el.name.replace(/allocations\[\d+\]/, `allocations[${idx}]`);
                    });
                    const rm = card.querySelector('.remove-allocation');
                    if (rm) rm.style.display = rows.querySelectorAll('.allocation-card').length > 1 ? '' : 'none';
                });
            }

            function addRow(data = {}) {
                if (rows.querySelectorAll('.allocation-card').length >= 20) return;
                const idx = rows.querySelectorAll('.allocation-card').length;
                let html = template.replaceAll('__INDEX__', idx).replaceAll('__NUMBER__', idx + 1);
                rows.insertAdjacentHTML('beforeend', html);
                const card = rows.lastElementChild;

                // Assign snapshot to new beneficiary select and immediately apply current program filter
                const benSel = card.querySelector('.allocation-beneficiary');
                if (benSel) benSel._originalOptions = templateBeneficiaryOptions;

                // default date: first row's date or today
                let defaultDate = todayISO();
                const allDates = rows.querySelectorAll('.allocation-date');
                if (allDates.length > 1) {
                    const firstVal = allDates[0].value;
                    if (firstVal) defaultDate = firstVal;
                }
                const dateInput = card.querySelector('.allocation-date');
                if (data.date_allocated) dateInput.value = data.date_allocated;
                else if (!dateInput.value) dateInput.value = defaultDate;

                if (data.beneficiary_id) benSel.value = data.beneficiary_id;
                if (data.amount) card.querySelector('.allocation-amount').value = data.amount;
                if (data.amount_cents) card.querySelector('.allocation-amount').value = (parseInt(data.amount_cents,10)/100).toFixed(2);
                if (data.notes) card.querySelector('.allocation-notes').value = data.notes;

                card.querySelector('.remove-allocation').addEventListener('click', () => { card.remove(); reindex(); });

                reindex();
                // Apply current program filter to the newly added row (and keep sources in sync)
                filterByProgram();
                // Restore beneficiary value if it was valid under filter
                if (data.beneficiary_id) {
                    const stillExists = benSel && Array.from(benSel.options).some(o => o.value === String(data.beneficiary_id));
                    if (stillExists) benSel.value = data.beneficiary_id;
                }
            }

            radios.forEach(r => r.addEventListener('change', syncSourceFields));
            donationSelect?.addEventListener('change', syncAllowance);
            grantSelect?.addEventListener('change', syncAllowance);
            programFilter?.addEventListener('change', filterByProgram);

            addBtn.addEventListener('click', () => addRow());

            // Defer initial filter until Choices is initialized so snapshot stays full
            function initFilters() {
                // If Choices already captured full snapshot, don't recapture; otherwise capture
                if (donationSelect && !donationSelect._originalOptions) captureOriginal(donationSelect);
                if (grantSelect && !grantSelect._originalOptions) captureOriginal(grantSelect);
                syncSourceFields();
                filterByProgram();
            }

            // initial rows (beneficiary rows don't depend on Choices)
            if (existingAllocations.length) {
                existingAllocations.forEach(r => addRow(r));
            } else {
                addRow();
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    // ensure app.js initializeChoices ran first
                    if (window.initializeChoices) window.initializeChoices();
                    setTimeout(initFilters, 0);
                }, { once: true });
            } else {
                if (window.initializeChoices) window.initializeChoices();
                setTimeout(initFilters, 0);
            }
        })();
    </script>
</x-dashboardlayout>
