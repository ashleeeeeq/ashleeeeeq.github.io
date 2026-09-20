<x-dashboardlayout title="Add Funding Target">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <a href="{{ route('funding.targets') }}" class="inline-flex items-center gap-1 text-sm transition-colors hover:text-white" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                        </svg>
                        Back to Targets
                    </a>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight" style="font-family: var(--font-header1); color: var(--color-white);">
                    Add Funding Target
                </h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                    Create an annual target for a specific program or for the organization
                </p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
        </div>

        @include('funding._tabs')

        <!-- Form Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v7.5m2.25-6.466a9.016 9.016 0 0 0-3.461-.203c-.536.072-.974.478-1.021 1.017a4.559 4.559 0 0 0-.018.402c0 .464.336.844.775.994l2.95 1.012c.44.15.775.53.775.994 0 .136-.006.27-.018.402-.047.539-.485.945-1.021 1.017a9.077 9.077 0 0 1-3.461-.203M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-white text-sm sm:text-base" style="font-family: var(--font-header1);">Target Information</h2>
                        <p class="text-xs" style="color: rgba(255,255,255,0.5);">Enter the funding target details</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('funding.targets.store') }}" method="POST" class="p-4 sm:p-6 space-y-5">
                @csrf

                <div class="grid gap-4 sm:gap-5 grid-cols-1 sm:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Program</label>
                        <div class="relative">
                            <select name="program_id" 
                                class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 appearance-none cursor-pointer hover:border-accent1"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;">
                                <option value="" style="color: var(--color-primary1); background-color: var(--color-white);">Organization-wide</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" style="color: var(--color-primary1); background-color: var(--color-white);" @selected(old('program_id') == $program->id)>{{ $program->program_name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 transition-colors duration-200" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                </svg>
                            </div>
                        </div>
                        <x-forms.errors name="program_id" />
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Year <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                        <input type="number" name="year" value="{{ old('year', $currentYear) }}" min="2000" max="2100" 
                            class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                            required>
                        <x-forms.errors name="year" />
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Target Amount <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-primary1">₱</span>
                            <input type="number" step="0.01" name="target_amount" value="{{ old('target_amount') }}" 
                                class="w-full pl-7 pr-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                                placeholder="0.00" required>
                        </div>
                        <x-forms.errors name="target_amount" />
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Currency</label>
                        <input type="text" name="currency" value="PHP" 
                            class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                            readonly>
                        <x-forms.errors name="currency" />
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Notes</label>
                        <textarea name="notes" rows="4" 
                            class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 resize-y hover:border-accent1"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;"
                            placeholder="Optional notes about this funding target...">{{ old('notes') }}</textarea>
                        <x-forms.errors name="notes" />
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4">
                    <a href="{{ route('funding.targets') }}" 
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
                        onmouseover="this.style.filter='brightness(0.95)'"
                        onmouseout="this.style.filter='brightness(1)'">
                        <svg class="w-4 h-4 transition-all duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save Target
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-dashboardlayout>