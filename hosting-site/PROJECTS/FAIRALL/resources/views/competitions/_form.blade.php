@php
    $selectedProgramId = old('program_id', $competition->program_id);
    $selectedType = old('type', $competition->type);
    $selectedScale = old('scale', $competition->scale);
    $startDate = old('start_date', optional($competition->start)->format('Y-m-d') ?? null);
    $startTime = old('start_time', optional($competition->start)->format('H:i') ?? null);
    $endDate = old('end_date', optional($competition->end)->format('Y-m-d') ?? null);
    $endTime = old('end_time', optional($competition->end)->format('H:i') ?? null);
@endphp

<!-- Competition Details Section -->
<div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
    <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Competition Details</h3>
                <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Configure the competition information</p>
            </div>
        </div>
    </div>
    <div class="p-6 space-y-5">
        <div>
            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Name <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
            <input type="text" name="name"
                class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                value="{{ old('name', $competition->name) }}" placeholder="Enter name" required>
            <x-forms.errors name="name" />
        </div>

        @if ($showProgramSelector)
            <div>
                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Program <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                <div class="relative">
                    <select name="program_id"
                        class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all duration-200 appearance-none cursor-pointer hover:border-accent1"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;"
                        required>
                        <option value="" style="color: var(--color-primary1); background-color: var(--color-primary1);">Select program</option>
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

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Type <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                <div class="relative">
                    <select name="type"
                        class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all duration-200 appearance-none cursor-pointer hover:border-accent1"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;"
                        required>
                        <option value="" style="color: var(--color-primary1); background-color: var(--color-primary1);">Select type</option>
                        @foreach ($competitionTypes as $type)
                            <option value="{{ $type }}" style="color: var(--color-primary1); background-color: white;"
                                @selected($selectedType === $type)>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-4 h-4 transition-colors duration-200" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                        </svg>
                    </div>
                </div>
                <x-forms.errors name="type" />
            </div>

            <div>
                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Scale <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                <div class="relative">
                    <select name="scale"
                        class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all duration-200 appearance-none cursor-pointer hover:border-accent1"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;"
                        required>
                        <option value="" style="color: var(--color-primary1); background-color: var(--color-primary1);">Select scale</option>
                        @foreach ($competitionScales as $scale)
                            <option value="{{ $scale }}" style="color: var(--color-primary1); background-color: white;"
                                @selected($selectedScale === $scale)>{{ ucfirst($scale) }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-4 h-4 transition-colors duration-200" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                        </svg>
                    </div>
                </div>
                <x-forms.errors name="scale" />
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Organizer <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                <input type="text" name="organizer"
                    class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                    value="{{ old('organizer', $competition->organizer) }}" placeholder="Enter organizer" required>
                <x-forms.errors name="organizer" />
            </div>

            <div>
                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Venue <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                <input type="text" name="venue"
                    class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                    value="{{ old('venue', $competition->venue) }}" placeholder="Enter venue" required>
                <x-forms.errors name="venue" />
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Start Date <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                <input type="date" name="start_date"
                    class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                    value="{{ $startDate }}" required>
                <x-forms.errors name="start_date" />
            </div>

            <div>
                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Start Time <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                <input type="time" name="start_time"
                    class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                    value="{{ $startTime }}" required>
                <x-forms.errors name="start_time" />
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">End Date</label>
                <input type="date" name="end_date"
                    class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                    value="{{ $endDate }}">
                <x-forms.errors name="end_date" />
            </div>

            <div>
                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">End Time</label>
                <input type="time" name="end_time"
                    class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                    value="{{ $endTime }}">
                <x-forms.errors name="end_time" />
            </div>
        </div>

        <div>
            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Description</label>
            <textarea name="description"
                class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                rows="4"
                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white; resize: vertical;"
                placeholder="Enter competition description (optional)">{{ old('description', $competition->description) }}</textarea>
            <x-forms.errors name="description" />
        </div>
    </div>
</div>