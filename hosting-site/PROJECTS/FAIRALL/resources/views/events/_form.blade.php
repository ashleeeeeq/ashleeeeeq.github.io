@php
    $selectedProgramId = old('program_id', $event->program_id);
    $selectedEventTypeId = old('event_type_id', $event->event_type_id);
@endphp

<!-- Event Details Section -->
<div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
    <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Event Details</h3>
                <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Configure the event schedule and information</p>
            </div>
        </div>
    </div>
    <div class="p-6 space-y-5">
        <div>
            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Name <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
            <input type="text" name="name"
                class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                value="{{ old('name', $event->name) }}" placeholder="Enter name" required>
            <x-forms.errors name="name" />
        </div>

        @if ($showProgramSelector)
            <div>
                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Program <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                <div class="relative">
                    <select name="program_id"
                        class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all duration-200 appearance-none cursor-pointer hover:border-accent1"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;">
                        <option value="" style="color: var(--color-primary1); background-color: var(--color-primary1);">No program</option>
                        @foreach ($programs as $program)
                            <option value="{{ $program->id }}" style="color: var(--color-primary1); background-color: white;"
                                @selected((string) $selectedProgramId === (string) $program->id)>{{ $program->program_name }}</option>
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
        @else
            <input type="hidden" name="program_id" value="{{ $userProgram?->id }}">
            <div class="rounded-xl p-4 flex items-start gap-3 transition-all duration-200 hover:bg-white/5"
                style="background-color: rgba(255,204,51,0.05); border: 1px solid rgba(255,204,51,0.15);">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-accent1);">Selected Program</span>
                    <p class="text-sm font-medium mt-0.5" style="color: white;">{{ $userProgram?->program_name ?? 'No program selected' }}</p>
                </div>
            </div>
        @endif

        <div>
            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Event Type <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
            <div class="relative">
                <select name="event_type_id"
                    class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all duration-200 appearance-none cursor-pointer hover:border-accent1"
                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;"
                    required>
                    <option value="" style="color: var(--color-primary1); background-color: var(--color-primary1);">Select type</option>
                    @foreach ($eventTypes as $eventType)
                        <option value="{{ $eventType->id }}" style="color: var(--color-primary1); background-color: white;"
                            @selected((string) $selectedEventTypeId === (string) $eventType->id)>{{ $eventType->name }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-4 h-4 transition-colors duration-200" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                    </svg>
                </div>
            </div>
            <x-forms.errors name="event_type_id" />
        </div>

        <div>
            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Location</label>
            <input type="text" name="location"
                class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                value="{{ old('location', $event->location) }}" placeholder="Enter event location">
            <x-forms.errors name="location" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Start Date & Time <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                <input type="datetime-local" name="start"
                    class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                    value="{{ old('start', optional($event->start)->format('Y-m-d\TH:i')) }}" required>
                <x-forms.errors name="start" />
            </div>

            <div>
                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">End Date & Time</label>
                <input type="datetime-local" name="end"
                    class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                    value="{{ old('end', optional($event->end)->format('Y-m-d\TH:i')) }}" placeholder="Optional">
                <x-forms.errors name="end" />
            </div>
        </div>

        <div>
            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Description</label>
            <textarea name="description"
                class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                rows="4"
                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white; resize: vertical;"
                placeholder="Enter event description (optional)">{{ old('description', $event->description) }}</textarea>
            <x-forms.errors name="description" />
        </div>
    </div>
</div>