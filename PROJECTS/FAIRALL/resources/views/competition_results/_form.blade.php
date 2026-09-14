@php
    $selectedBeneficiaryId = old('beneficiary_id', null);
    $placement = old('placement', '');
    $dateGiven = old('date_given', now()->toDateString());
@endphp

<!-- Add Competition Result Section -->
<div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
    <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Add Competition Result</h3>
                <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Record a new competition achievement</p>
            </div>
        </div>
    </div>
    <div class="p-6 space-y-5">
        <div>
            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Beneficiary <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
            <div class="relative">
                <select name="beneficiary_id" data-choices="true"
                        class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all duration-200 appearance-none cursor-pointer hover:border-accent1" 
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;" 
                        required>
                    <option value="" style="color: var(--color-primary1); background-color: var(--color-primary1);">Select beneficiary</option>
                    @foreach ($beneficiaries as $beneficiary)
                        <option value="{{ $beneficiary->id }}" style="color: var(--color-primary1); background-color: white;" @selected((string) $selectedBeneficiaryId === (string) $beneficiary->id)>
                            {{ $beneficiary->display_name }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-4 h-4 transition-colors duration-200" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                    </svg>
                </div>
            </div>
            <x-forms.errors name="beneficiary_id" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Placement/Achievement <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                <input type="text" name="placement" 
                       class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1" 
                       style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                       value="{{ $placement }}" 
                       placeholder="e.g., 1st Place, Gold Medal, Champion" 
                       required 
                       maxlength="100">
                <x-forms.errors name="placement" />
            </div>

            <div>
                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Date Given</label>
                <input type="date" name="date_given" 
                       class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1" 
                       style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                       value="{{ $dateGiven }}">
                <x-forms.errors name="date_given" />
            </div>
        </div>
    </div>
</div>