<x-dashboardlayout name="{{ $name }}" title="New Donation">
    <div class="mb-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight" style="font-family: var(--font-header1); color: var(--color-white);">New Donation</h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">Record staff-managed donation(s) in the all-donations ledger.</p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
        </div>

        <form method="POST" action="{{ route('donors.donations.store-all') }}" class="space-y-4" id="donations-form">
            @csrf

            <div id="donations-rows" class="space-y-4"></div>

            <div class="flex justify-start mt-2">
                <button type="button" id="add-donation"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02]"
                    style="font-family: var(--font-body1); background-color: transparent; color: var(--color-accent1); border: 1px solid var(--color-accent1);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Donation
                </button>
            </div>
            <x-forms.errors name="donations" />

            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4 border-t" style="border-color: rgba(255,255,255,0.08);">
                <a href="{{ route('donors.donations.all') }}"
                   class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] text-center inline-flex items-center justify-center gap-2"
                   style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center justify-center gap-2"
                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                    Save Donation(s)
                </button>
            </div>
        </form>
    </div>

    <template id="donation-template">
        <div class="donation-card rounded-2xl overflow-hidden" style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-6 py-4 border-b flex items-center justify-between" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                        <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v7.5m2.25-6.466a9.016 9.016 0 0 0-3.461-.203c-.536.072-.974.478-1.021 1.017a4.559 4.559 0 0 0-.018.402c0 .464.336.844.775.994l2.95 1.012c.44.15.775.53.775.994 0 .136-.006.27-.018.402-.047.539-.485.945-1.021 1.017a9.077 9.077 0 0 1-3.461-.203M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"></path></svg>
                    </div>
                    <h2 class="font-semibold text-white donation-card-title" style="font-family: var(--font-header1);">Donation #__NUMBER__</h2>
                </div>
                <button type="button" class="remove-donation inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-lg transition-all duration-200" style="font-family: var(--font-body1); color: var(--color-danger); background-color: rgba(248,113,113,0.12); border: 1px solid rgba(248,113,113,0.2);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Remove
                </button>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Donor</label>
                        <div class="relative">
                            <select name="donations[__INDEX__][donor_id]" class="donation-donor w-full rounded-xl px-4 py-2.5 appearance-none transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1 cursor-pointer" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">
                                <option value="" style="background: var(--color-white); color: var(--color-primary1);">N/A (Anonymous)</option>
                                @foreach ($donors as $listedDonor)
                                    <option value="{{ $listedDonor->id }}" style="background: var(--color-white); color: var(--color-primary1);">{{ $listedDonor->display_name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"><svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path></svg></div>
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Donation Type</label>
                        <div class="relative">
                            <select name="donations[__INDEX__][donation_type]" class="donation-type w-full rounded-xl px-4 py-2.5 appearance-none transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1 cursor-pointer" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">
                                <option value="financial" style="background: var(--color-white); color: var(--color-primary1);">Financial</option>
                                <option value="in-kind" style="background: var(--color-white); color: var(--color-primary1);">In-kind</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"><svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path></svg></div>
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Amount <span style="color: var(--color-danger);">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2" style="color: var(--color-primary1);">₱</span>
                            <input type="number" step="0.01" name="donations[__INDEX__][amount]" class="donation-amount w-full rounded-xl pl-8 pr-4 py-2.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: var(--color-primary1);" placeholder="0.00" required>
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Transaction Date <span style="color: var(--color-danger);">*</span></label>
                        <input type="date" name="donations[__INDEX__][transaction_date]" class="donation-date w-full rounded-xl px-4 py-2.5 text-primary1 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);" required>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Status <span style="color: var(--color-danger);">*</span></label>
                        <div class="relative">
                            <select name="donations[__INDEX__][status]" class="donation-status w-full rounded-xl px-4 py-2.5 appearance-none transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1 cursor-pointer" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;" required>
                                <option value="pending" style="background: var(--color-white); color: var(--color-primary1);">Pending</option>
                                <option value="completed" style="background: var(--color-white); color: var(--color-primary1);">Completed</option>
                                <option value="failed" style="background: var(--color-white); color: var(--color-primary1);">Failed</option>
                                <option value="refunded" style="background: var(--color-white); color: var(--color-primary1);">Refunded</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"><svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path></svg></div>
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Program</label>
                        <div class="relative">
                            <select name="donations[__INDEX__][program_id]" class="donation-program w-full rounded-xl px-4 py-2.5 appearance-none transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1 cursor-pointer" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">
                                <option value="" style="background: var(--color-white); color: var(--color-primary1);">Select program</option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->id }}" style="background: var(--color-white); color: var(--color-primary1);">{{ $program->program_name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"><svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path></svg></div>
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Reference Number</label>
                        <input type="text" name="donations[__INDEX__][reference_number]" class="donation-ref w-full rounded-xl px-4 py-2.5 text-primary1 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);" placeholder="Optional">
                    </div>
                </div>
                <div>
                    <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Description</label>
                    <textarea name="donations[__INDEX__][description]" rows="3" class="donation-desc w-full rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1 resize-y" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;" placeholder="Enter description..."></textarea>
                </div>
            </div>
        </div>
    </template>

    <script>
        (() => {
            const existing = @json(old('donations', []));
            const rows = document.getElementById('donations-rows');
            const template = document.getElementById('donation-template').innerHTML;
            const addBtn = document.getElementById('add-donation');
            const reindex = () => {
                rows.querySelectorAll('.donation-card').forEach((card, idx) => {
                    card.querySelector('.donation-card-title').textContent = `Donation #${idx + 1}`;
                    card.querySelectorAll('input, select, textarea').forEach(el => {
                        if (el.name) el.name = el.name.replace(/donations\[\d+\]/, `donations[${idx}]`);
                    });
                    const rm = card.querySelector('.remove-donation');
                    if (rm) rm.style.display = rows.querySelectorAll('.donation-card').length > 1 ? '' : 'none';
                });
            };
            const addRow = (data = {}) => {
                if (rows.querySelectorAll('.donation-card').length >= 20) return;
                const idx = rows.querySelectorAll('.donation-card').length;
                let html = template.replaceAll('__INDEX__', idx).replaceAll('__NUMBER__', idx + 1);
                rows.insertAdjacentHTML('beforeend', html);
                const card = rows.lastElementChild;
                if (data.donor_id) card.querySelector('.donation-donor').value = data.donor_id;
                if (data.donation_type) card.querySelector('.donation-type').value = data.donation_type;
                if (data.amount) card.querySelector('.donation-amount').value = data.amount;
                if (data.transaction_date) card.querySelector('.donation-date').value = data.transaction_date;
                if (data.status) card.querySelector('.donation-status').value = data.status;
                if (data.program_id) card.querySelector('.donation-program').value = data.program_id;
                if (data.reference_number) card.querySelector('.donation-ref').value = data.reference_number;
                if (data.description) card.querySelector('.donation-desc').value = data.description;
                card.querySelector('.remove-donation').addEventListener('click', () => { card.remove(); reindex(); });
                reindex();
            };
            addBtn.addEventListener('click', () => addRow());
            if (existing.length) existing.forEach(r => addRow(r)); else addRow();
        })();
    </script>
</x-dashboardlayout>
