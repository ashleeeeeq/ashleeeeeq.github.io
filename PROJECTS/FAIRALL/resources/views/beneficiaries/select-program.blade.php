<x-dashboardlayout name="{{ $name }}" title="Select Program">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight" style="font-family: var(--font-header1); color: var(--color-white);">
                    Select Program
                </h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                    Choose a program to enroll a new beneficiary
                </p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white text-sm sm:text-base" style="font-family: var(--font-header1);">Program Selection</h3>
                        <p class="text-xs" style="color: rgba(255,255,255,0.5);">Select the program for beneficiary enrollment</p>
                    </div>
                </div>
            </div>
            <div class="p-4 sm:p-6">
                <form method="GET" action="/beneficiaries/create" class="space-y-6">
                    <div>
                        <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Program <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                        <div class="relative">
                            <select name="program_id" 
                                class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 appearance-none cursor-pointer hover:border-accent1"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;" required>
                                <option value="" disabled selected style="color: var(--color-primary1); background-color: var(--color-white);">Select a program</option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->id }}" style="color: var(--color-primary1); background-color: var(--color-white);">{{ $program->program_name }}</option>
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

                    <!-- Form Actions -->
                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4">
                        <a href="/beneficiaries" 
                            class="px-6 py-3 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] text-center inline-flex items-center justify-center gap-2 group"
                            style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                            onmouseover="this.style.backgroundColor='rgba(248,113,113,0.1)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)'"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-danger)'; this.style.borderColor='var(--color-danger)'">
                            <svg class="w-4 h-4 transition-all duration-300 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </a>
                        <button type="submit" 
                            class="px-6 py-3 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center justify-center gap-2 group"
                            style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                            onmouseover="this.style.filter='brightness(0.95)'"
                            onmouseout="this.style.filter='brightness(1)'">
                            <svg class="w-4 h-4 transition-all duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"></path>
                            </svg>
                            Continue
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-dashboardlayout>