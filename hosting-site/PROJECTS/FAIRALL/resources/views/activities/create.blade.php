<x-dashboardlayout name="{{ $name }}" title="Create Activity">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight" style="font-family: var(--font-header1); color: var(--color-white);">Create Activity</h1>
                <p class="text-sm mt-2" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">Define the activity and assign it to a program</p>
                <div class="w-20 h-1 mt-3 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl flex items-start gap-3" style="background-color: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.3);">
                <svg class="w-5 h-5 shrink-0 mt-0.5" style="color: var(--color-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                </svg>
                <div>
                    <span class="text-sm font-semibold" style="color: var(--color-danger);">Please fix the following errors:</span>
                    <ul class="mt-1 text-sm space-y-1" style="color: rgba(255,255,255,0.7);">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="/activities" class="space-y-6" id="activityForm">
            @csrf

            <!-- Activity Details Card -->
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Activity Details</h3>
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Basic activity information</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Name <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                        <input name="name"
                            class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                            placeholder="Enter activity name" required>
                    </div>

                    @if ($showProgramSelector)
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Program <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <div class="grid gap-3 sm:grid-cols-2">
                                @foreach ($programs as $program)
                                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border p-3 transition-all duration-200 hover:bg-white/5 hover:border-accent1/50"
                                        style="border-color: rgba(255,255,255,0.15); background-color: rgba(255,255,255,0.02);">
                                        <input type="radio" name="program_id" value="{{ $program->id }}"
                                            class="program-selector w-4 h-4 transition-all cursor-pointer" style="accent-color: var(--color-accent1);" required
                                            data-program-name="{{ Str::lower(trim($program->program_name)) }}">
                                        <span class="text-sm" style="font-family: var(--font-body1); color: rgba(255,255,255,0.85);">{{ $program->program_name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="program_id" value="{{ $userProgram?->id }}"
                            data-user-program-name="{{ Str::lower(trim($userProgram?->program_name)) }}">
                        <div class="rounded-xl p-4 flex items-start gap-3 transition-all duration-200 hover:bg-white/5"
                            style="background-color: rgba(255,204,51,0.05); border: 1px solid rgba(255,204,51,0.15);">
                            <svg class="w-5 h-5 shrink-0 mt-0.5" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-accent1);">Selected Program</span>
                                <p class="text-sm font-medium mt-0.5" style="color: white;">{{ $userProgram?->program_name ?? 'No program selected' }}</p>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Activity Type <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                        <div class="relative">
                            <select name="activity_type_id"
                                class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all duration-200 appearance-none cursor-pointer hover:border-accent1"
                                id="activityTypeSelect"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;"
                                required>
                                <option value="" style="color: var(--color-secondary-dark1);">Select activity type</option>
                                @foreach ($activityTypes as $activityType)
                                    @if ($showProgramSelector || !$activityType->program_id || $activityType->program_id === $userProgram?->id)
                                        <option value="{{ $activityType->id }}"
                                            data-program-id="{{ $activityType->program_id }}"
                                            style="color: var(--color-primary1);">
                                            {{ $activityType->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 transition-colors duration-200" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div id="sportTypeControl" style="display: none;">
                        <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Sport Type</label>
                        <div class="relative">
                            <select name="sport_type_id"
                                class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all duration-200 appearance-none cursor-pointer hover:border-accent1"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;">
                                <option value="" style="color: var(--color-secondary-dark1);">Select sport type (optional)</option>
                                @foreach ($sportTypes as $sportType)
                                    <option value="{{ $sportType->id }}" style="color: var(--color-primary1);">{{ $sportType->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 transition-colors duration-200" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Description</label>
                        <textarea name="description"
                            class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                            rows="4"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white; resize: vertical;"
                            placeholder="Enter activity description (optional)"></textarea>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4">
                <a href="/activities"
                   class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] justify-center inline-flex items-center gap-2 group"
                   style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                   onmouseover="this.style.backgroundColor='rgba(248,113,113,0.1)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)'"
                   onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-danger)'; this.style.borderColor='var(--color-danger)'">
                    <svg class="w-4 h-4 transition-all duration-300 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center gap-2 group justify-center"
                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                        onmouseover="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(0.95)'"
                        onmouseout="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(1)'">
                    <svg class="w-4 h-4 transition-all duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                    </svg>
                    Save Activity
                </button>
            </div>
        </form>

        <script>
            const programSelectors = document.querySelectorAll('.program-selector');
            const activityTypeSelect = document.getElementById('activityTypeSelect');
            const sportTypeControl = document.getElementById('sportTypeControl');
            const sportTypeSelect = document.querySelector('select[name="sport_type_id"]');
            const hiddenProgramInput = document.querySelector('input[name="program_id"][type="hidden"]');
            const userProgramName = hiddenProgramInput?.getAttribute('data-user-program-name') || '';

            function filterActivityTypes() {
                if (!activityTypeSelect) return;
                const checkedRadio = document.querySelector('input[name="program_id"]:checked');
                const selectedProgramId = checkedRadio?.value || hiddenProgramInput?.value || '';
                const selectedProgramName = checkedRadio?.getAttribute('data-program-name') || userProgramName;
                const options = activityTypeSelect.querySelectorAll('option');
                const isSportsProgram = selectedProgramName?.includes('sports') || userProgramName?.includes('sports');

                sportTypeControl.style.display = isSportsProgram ? 'block' : 'none';

                if (!isSportsProgram && sportTypeSelect) {
                    sportTypeSelect.value = '';
                }

                options.forEach(option => {
                    if (option.value === '') return;

                    const optionProgramId = option.getAttribute('data-program-id');
                    const isVisible = !optionProgramId || optionProgramId === selectedProgramId;
                    option.style.display = isVisible ? '' : 'none';
                });

                const activeOption = activityTypeSelect.selectedOptions[0];
                if (activeOption && activeOption.style.display === 'none') {
                    activityTypeSelect.value = '';
                }
            }

            programSelectors.forEach(selector => {
                selector.addEventListener('change', filterActivityTypes);
            });

            filterActivityTypes();
        </script>
    </div>
</x-dashboardlayout>