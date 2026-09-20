<x-dashboardlayout title="Edit Allocation #{{ $allocation->id }}">
    <div class="mb-8 space-y-6 space-y-6">
        <!-- Header Section -->
        <div>
            <a href="{{ route('funding.allocations.show', $allocation) }}" class="text-sm text-white/60 hover:text-white transition-colors inline-flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to allocation
            </a>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight" style="font-family: var(--font-header1); color: var(--color-white);">Edit Allocation #{{ $allocation->id }}</h1>
            <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">Update allocation details and amounts</p>
            <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
        </div>


        @include('funding._tabs')


        <form action="{{ route('funding.allocations.update', $allocation) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="return_url" value="{{ old('return_url', session('return_url')) }}">


            <!-- Allowable Amount Card -->
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Allowable Amount</h2>
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Available funds from source</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="rounded-xl p-5" style="background-color: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.06);">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-wider text-white/50">Available Amount</div>
                                <div class="mt-1 text-3xl font-bold text-white" style="font-family: var(--font-header1);">₱{{ number_format(($sourceSummary['available_cents'] ?? 0) / 100, 2) }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-white/40">{{ ucfirst($sourceSummary['type'] ?? 'source') }} {{ $sourceSummary['label'] ?? '' }}</div>
                                <div class="text-xs text-white/40 mt-0.5">
                                    Allocated: ₱{{ number_format(($sourceSummary['allocated_cents'] ?? 0) / 100, 2) }} of ₱{{ number_format(($sourceSummary['total_cents'] ?? 0) / 100, 2) }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t" style="border-color: rgba(255,255,255,0.06);">
                            <div class="text-sm text-white/60">
                                Editable maximum for this allocation:
                                <span class="font-semibold text-white">₱{{ number_format(($sourceSummary['editable_max_cents'] ?? 0) / 100, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Edit Form Card -->
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Edit Allocation</h2>
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Update allocation details below</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-5">
                    <!-- Beneficiary -->
                    <div>
                        <label class="block mb text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Beneficiary <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                        <div class="relative">
                            <span class="text-sm text-white/50">Beneficiary list is scoped to the same program the grant is linked to.</span>
                            <select name="beneficiary_id" id="beneficiary_id" data-choices="true"
                                class="w-full rounded-xl px-4 py-2.5 appearance-none transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1 cursor-pointer"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;"
                                required>
                                <option value=""
                                    style="background-color: var(--color-primary1); color: white;">Select a
                                    beneficiary</option>
                                @foreach ($beneficiaries as $beneficiary)
                                    <option value="{{ $beneficiary->id }}"
                                        style="background-color: white; color: var(--color-primary1);"
                                        @selected(old('beneficiary_id', $allocation->beneficiary_id) == $beneficiary->id)
                                        data-program-ids="{{ $beneficiary->programs->pluck('id')->implode(',') }}">
                                        {{ $beneficiary->display_name }}
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

                    <div class="grid gap-5 md:grid-cols-2">
                        <!-- Allocated Amount -->
                        <div>
                            <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Allocated Amount <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-primary1">₱</span>
                                <input type="number" step="0.01" name="amount" value="{{ old('amount', number_format($allocation->amount_cents / 100, 2, '.', '')) }}"
                                       class="w-full rounded-xl px-7.5 py-2.5 text-primary1 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1"
                                       style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);"
                                       placeholder="0.00" required
                                       data-max-cents="{{ $sourceSummary['editable_max_cents'] ?? 0 }}">
                            </div>
                        </div>

                        <div>
                            <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Allocation Date <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="date" name="date_allocated" value="{{ old('date_allocated', optional($allocation->date_allocated)->format('Y-m-d')) }}"
                                   class="w-full rounded-xl px-4 py-2.5 text-primary1 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1"
                                   style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);" required>
                        </div>
                    </div>


                    <div>
                        <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Notes</label>
                        <textarea name="notes" rows="4"
                                  class="w-full rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1 resize-y"
                                  style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;"
                                  placeholder="Enter notes (optional)">{{ old('notes', $allocation->notes) }}</textarea>
                    </div>
                </div>
            </div>


            <!-- Form Actions -->
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4">
                <a href="{{ route('funding.allocations.show', $allocation) }}"
                   class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] text-center inline-flex items-center justify-center gap-2 group"
                   style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                   onmouseover="this.style.backgroundColor='rgba(248,113,113,0.1)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)'"
                   onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-danger)'; this.style.borderColor='var(--color-danger)'">
                    <svg class="w-4 h-4 transition-all duration-300 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center justify-center gap-2 group"
                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                        onmouseover="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(0.95)'"
                        onmouseout="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(1)'">
                    <svg class="w-4 h-4 transition-all duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <script>
        (function() {
            const amountInput = document.querySelector('input[name="amount"]');
            if (!amountInput) return;

            const maxCents = parseInt(amountInput.dataset.maxCents, 10) || 0;

            function clampAmount() {
                if (maxCents <= 0) return;

                const raw = amountInput.value.replace(/[₱,\s]/g, '');
                const inputCents = Math.round(parseFloat(raw || 0) * 100);

                if (inputCents > maxCents) {
                    const clamped = (maxCents / 100).toFixed(2);
                    amountInput.value = clamped;
                    amountInput.style.borderColor = 'var(--color-danger)';
                    amountInput.style.backgroundColor = 'rgba(248,113,113,0.1)';
                    setTimeout(() => {
                        amountInput.style.borderColor = 'rgba(255,255,255,0.1)';
                        amountInput.style.backgroundColor = 'rgba(255,255,255,0.05)';
                    }, 1200);
                }
            }

            amountInput.addEventListener('change', clampAmount);
            amountInput.addEventListener('blur', clampAmount);
        })();
    </script>
</x-dashboardlayout>

