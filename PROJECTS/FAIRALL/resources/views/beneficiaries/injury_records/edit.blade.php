<x-dashboardlayout name="{{ $name }}" title="Edit Injury Record">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Edit Injury Record</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Update injury record for {{ $beneficiary->display_name }}</p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        <form method="POST" action="/beneficiaries/{{ $beneficiary->id }}/injury-records/{{ $record->id }}">
            @csrf
            @method('PUT')

            <!-- Injury Details Card -->
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Injury Details</h2>
                            <p class="text-xs text-white/50">Edit the injury and recovery information</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Injury Type -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Injury Type <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="text" name="injury_type" value="{{ old('injury_type', $record->injury_type) }}" 
                                   class="w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all" 
                                   style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                                   placeholder="e.g., Fracture, Sprain, Concussion" required>
                            <x-forms.errors name="injury_type" />
                        </div>

                        <!-- Severity -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Severity <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <div class="relative">
                                <select name="severity" class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none" 
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;" required>
                                    <option value="" disabled style="color: var(--color-primary1);">Select severity</option>
                                    <option value="minor" @if(old('severity', $record->severity) == 'minor') selected @endif style="color: var(--color-primary1);">Minor</option>
                                    <option value="moderate" @if(old('severity', $record->severity) == 'moderate') selected @endif style="color: var(--color-primary1);">Moderate</option>
                                    <option value="serious" @if(old('severity', $record->severity) == 'serious') selected @endif style="color: var(--color-primary1);">Serious</option>
                                    <option value="severe" @if(old('severity', $record->severity) == 'severe') selected @endif style="color: var(--color-primary1);">Severe</option>
                                    <option value="critical" @if(old('severity', $record->severity) == 'critical') selected @endif style="color: var(--color-primary1);">Critical</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                    </svg>
                                </div>
                            </div>
                            <x-forms.errors name="severity" />
                        </div>

                        <!-- Body Part -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Body Part <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="text" name="body_part" value="{{ old('body_part', $record->body_part) }}" 
                                   class="w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all" 
                                   style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                                   placeholder="e.g., Left Ankle, Right Wrist" required>
                            <x-forms.errors name="body_part" />
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Status <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <div class="relative">
                                <select name="status" class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none" 
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;" required>
                                    <option value="" disabled style="color: var(--color-primary1);">Select status</option>
                                    <option value="recovering" @if(old('status', $record->status) == 'recovering') selected @endif style="color: var(--color-primary1);">Recovering</option>
                                    <option value="recovered" @if(old('status', $record->status) == 'recovered') selected @endif style="color: var(--color-primary1);">Recovered</option>
                                    <option value="chronic" @if(old('status', $record->status) == 'chronic') selected @endif style="color: var(--color-primary1);">Chronic</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                    </svg>
                                </div>
                            </div>
                            <x-forms.errors name="status" />
                        </div>

                        <!-- Recovery Dates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Recovery Start Date <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                             <input type="date" name="recovery_start_date" value="{{ old('recovery_start_date', $record->recovery_start_date?->format('Y-m-d')) }}" 
                                   class="w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all" 
                                   style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                                   required>
                                <x-forms.errors name="recovery_start_date" />
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Recovery End Date (Optional)</label>
                                <input type="date" name="recovery_end_date" value="{{ old('recovery_end_date', $record->recovery_end_date?->format('Y-m-d')) }}" 
                                       class="w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all" 
                                       style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);">
                                <x-forms.errors name="recovery_end_date" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 pt-6 mt-6 border-t" style="border-color: rgba(255,255,255,0.1);">
                <a href="/beneficiaries/{{ $beneficiary->id }}/injury-records" 
                   class="px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] text-center"
                    style="
                        font-family: var(--font-body1); 
                        background-color: transparent; 
                        color: var(--color-danger); 
                        border: 1px solid var(--color-danger);"
                    onmouseover="
                        this.style.backgroundColor='var(--color-danger-light)'; 
                        this.style.color='var(--color-danger-dark)'; 
                        this.style.borderColor='var(--color-danger-dark)';"
                    onmouseout="
                        this.style.backgroundColor='transparent'; 
                        this.style.color='var(--color-danger)'; 
                        this.style.borderColor='var(--color-danger)';">
                        Cancel
                </a>

                <button type="submit" 
                        class="px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                    Update Injury Record
                </button>
            </div>
        </form>
    </div>
</x-dashboardlayout>