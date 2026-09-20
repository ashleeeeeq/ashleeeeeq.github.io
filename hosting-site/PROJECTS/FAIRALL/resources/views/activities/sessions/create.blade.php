<x-dashboardlayout name="{{ $name }}" title="Create Activity Session">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight" style="font-family: var(--font-header1); color: var(--color-white);">Create Activity Session</h1>
                <p class="text-sm mt-2" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">Create a session for {{ $activity->name }}</p>
                <div class="w-20 h-1 mt-3 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
        </div>

        <form method="POST" action="/activities/{{ $activity->id }}/sessions" class="space-y-6" id="sessionForm">
            @csrf

            <!-- Session Details Card -->
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Session Details</h3>
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Configure the session schedule</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-5 overflow-auto">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Schedule Date <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="date" name="schedule_date" class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1" id="scheduleDate" required
                                   style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Schedule Time <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="time" name="schedule_time" class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1" id="scheduleTime" required
                                   style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);">
                        </div>
                    </div>

                    <div>
                        <label class="inline-flex cursor-pointer items-center gap-3 rounded-xl border px-4 py-3 transition-all duration-200 hover:bg-white/5 hover:border-accent1/50"
                               style="border-color: rgba(255,255,255,0.15); background-color: rgba(255,255,255,0.02);">
                            <input type="checkbox" name="enable_repeat" id="enableRepeat" value="1" class="w-4 h-4 rounded transition-all" style="accent-color: var(--color-accent1);">
                            <span class="text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.85);">Repeat Session?</span>
                        </label>
                    </div>

                    <div id="repeatSessionOptions" class="space-y-5 hidden">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Repeat Until Date</label>
                                <input type="date" name="repeat_until_date" class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1" id="repeatUntilDate"
                                       style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);">
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Repeat Until Time</label>
                                <input type="time" name="repeat_until_time" class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1" id="repeatUntilTime"
                                       style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);">
                            </div>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Repeat on Days</label>
                            <div class="grid gap-3 sm:grid-cols-2">
                                @foreach ($weekdayLabels as $value => $label)
                                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border p-3 transition-all duration-200 hover:bg-white/5 hover:border-accent1/50" 
                                           style="border-color: rgba(255,255,255,0.15); background-color: rgba(255,255,255,0.02);">
                                        <input type="checkbox" name="repeat_days[]" value="{{ $value }}" class="w-4 h-4 rounded transition-all" style="accent-color: var(--color-accent1);">
                                        <span class="text-sm" style="font-family: var(--font-body1); color: rgba(255,255,255,0.85);">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div class="flex items-start gap-2 mt-3 p-3 rounded-xl" style="background-color: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.15);">
                                <svg class="w-4 h-4 shrink-0 mt-0.5" style="color: var(--color-info);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-xs" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">The first schedule is always created, then the system repeats only on the days you select until the end date.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4">
                <a href="/activities/{{ $activity->id }}" 
                   class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] justify-center inline-flex items-center gap-2 group"
                   style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                   onmouseover="this.style.backgroundColor='rgba(248,113,113,0.15)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)'"
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
                    Save Session
                </button>
            </div>
        </form>
    </div>

    <script>
        const scheduleTimeInput = document.getElementById('scheduleTime');
        const repeatSessionCheckbox = document.getElementById('enableRepeat');
        const repeatSessionOptions = document.getElementById('repeatSessionOptions');
        const repeatUntilTimeInput = document.getElementById('repeatUntilTime');
        let userModifiedRepeatUntilTime = false;

        function toggleRepeatSessionOptions() {
            const shouldShow = repeatSessionCheckbox.checked;
            repeatSessionOptions.classList.toggle('hidden', !shouldShow);

            repeatSessionOptions.querySelectorAll('input').forEach((input) => {
                input.disabled = !shouldShow;
            });
        }

        const hasRepeatValues = repeatUntilTimeInput.value
            || document.getElementById('repeatUntilDate').value
            || repeatSessionOptions.querySelector('input[name="repeat_days[]"]:checked');

        if (hasRepeatValues) {
            repeatSessionCheckbox.checked = true;
        }

        repeatSessionCheckbox.addEventListener('change', toggleRepeatSessionOptions);
        toggleRepeatSessionOptions();

        // Sync repeat_until_time to schedule_time on schedule_time change
        scheduleTimeInput.addEventListener('change', () => {
            if (!userModifiedRepeatUntilTime) {
                repeatUntilTimeInput.value = scheduleTimeInput.value;
            }
        });

        // Track if user has manually modified repeat_until_time
        repeatUntilTimeInput.addEventListener('input', () => {
            userModifiedRepeatUntilTime = true;
        });

        // Initialize repeat_until_time to match schedule_time on page load if empty
        if (scheduleTimeInput.value && !repeatUntilTimeInput.value) {
            repeatUntilTimeInput.value = scheduleTimeInput.value;
        }
    </script>
</x-dashboardlayout>