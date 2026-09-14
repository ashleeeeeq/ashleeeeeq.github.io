@php
    $selectedBeneficiaryId = old('beneficiary_id', $homeVisit->beneficiary_id ?? null);
    $selectedVisitType = old('visit_type', $homeVisit->visit_type ?? null);
    $selectedStaffId = old('assigned_staff_id', $homeVisit->assigned_staff_id ?? null);
    $scheduleDate = old('schedule_date', optional($homeVisit->schedule)->format('Y-m-d') ?? null);
    $scheduleTime = old('schedule_time', optional($homeVisit->schedule)->format('H:i') ?? null);
@endphp

@can('manage-home-visits')
    <!-- Home Visit Details Section -->
    <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
        <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                    <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Home Visit Details</h3>
                    <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Configure the home visit information</p>
                </div>
            </div>
        </div>
        <div class="p-6 space-y-5">
            @if($homeVisit->exists)
                <div>
                    <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Beneficiary</label>
                    <div class="flex items-center gap-3 p-3 rounded-xl" style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-5 h-5" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-white">{{ $homeVisit->beneficiary?->display_name ?? 'Unknown' }}</p>
                            <p class="text-xs text-white/50">Beneficiary</p>
                        </div>
                    </div>
                    <input type="hidden" name="beneficiary_id" value="{{ $homeVisit->beneficiary_id }}">
                </div>
            @else
                <div>
                    <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Beneficiary <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                    <div class="relative">
                        <select name="beneficiary_id" data-choices="true"
                            class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all duration-200 appearance-none cursor-pointer hover:border-accent1"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;"
                            required>
                            <option value="" style="color: var(--color-primary1); background-color: var(--color-primary1);">Select beneficiary</option>
                            @foreach ($beneficiaries as $beneficiary)
                                <option value="{{ $beneficiary->id }}" style="color: var(--color-primary1); background-color: white;"
                                    @selected((string) $selectedBeneficiaryId === (string) $beneficiary->id)>
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
            @endif

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Visit Type <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                    <div class="relative">
                        <select name="visit_type"
                            class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all duration-200 appearance-none cursor-pointer hover:border-accent1"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;"
                            required>
                            <option value="" style="color: var(--color-primary1); background-color: var(--color-primary1);">Select type</option>
                            @foreach ($visitTypes as $type)
                                <option value="{{ $type }}" style="color: var(--color-primary1); background-color: white;"
                                    @selected($selectedVisitType === $type)>
                                    {{ ucfirst(str_replace('-', ' ', $type)) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 transition-colors duration-200" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                            </svg>
                        </div>
                    </div>
                    <x-forms.errors name="visit_type" />
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Assign Staff</label>
                    <div class="relative">
                        @if (count($availableStaff) > 0)
                            <select name="assigned_staff_id" data-choices="true"
                                class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all duration-200 appearance-none cursor-pointer hover:border-accent1"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;">
                                <option value="{{ $currentStaffId }}" style="color: var(--color-primary1); background-color: var(--color-primary1);"
                                    @selected((string) $selectedStaffId === (string) $currentStaffId || !$selectedStaffId)>Self-assign</option>
                                @foreach ($availableStaff as $staffMember)
                                    @if ((int) $staffMember['id'] !== (int) $currentStaffId)
                                        <option value="{{ $staffMember['id'] }}" style="color: var(--color-primary1); background-color: white;"
                                            @selected((string) $selectedStaffId === (string) $staffMember['id'])>
                                            {{ $staffMember['first_name'] }} {{ $staffMember['last_name'] }}
                                            @if ($staffMember['position'])
                                                ({{ $staffMember['position']['name'] }})
                                            @endif
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 transition-colors duration-200" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                </svg>
                            </div>
                        @else
                            <div class="w-full px-4 py-2.5 rounded-xl text-sm"
                                style="background-color: rgba(255,204,51,0.05); border: 1px solid rgba(255,204,51,0.15); color: rgba(255,255,255,0.6);">
                                No staff available for assignment
                            </div>
                        @endif
                    </div>
                    <x-forms.errors name="assigned_staff_id" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Schedule Date <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                    <input type="date" name="schedule_date"
                        class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                        value="{{ $scheduleDate }}" required>
                    <x-forms.errors name="schedule_date" />
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Schedule Time <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                    <input type="time" name="schedule_time"
                        class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                        value="{{ $scheduleTime }}" required>
                    <x-forms.errors name="schedule_time" />
                </div>
            </div>

            <div>
                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Purpose <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                <input type="text" name="purpose"
                    class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                    value="{{ old('purpose', $homeVisit->purpose ?? '') }}" placeholder="Brief purpose of visit"
                    required maxlength="100">
                <x-forms.errors name="purpose" />
            </div>

            <div>
                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Notes</label>
                <textarea name="notes"
                    class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                    rows="4"
                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white; resize: vertical;"
                    placeholder="Notes during or after the visit" maxlength="500">{{ old('notes', $homeVisit->notes ?? '') }}</textarea>
                <x-forms.errors name="notes" />
            </div>
        </div>
    </div>
@else
    <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
        <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                    <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Assigned Home Visit</h3>
                    <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">View home visit details</p>
                </div>
            </div>
        </div>
        <div class="p-6 space-y-5">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider mb-2" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Beneficiary</div>
                    <div class="text-sm font-medium" style="color: white; font-family: var(--font-body1);">{{ $homeVisit->beneficiary?->display_name ?? 'Unknown' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider mb-2" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Visit Type</div>
                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" 
                          style="background-color: rgba(255,204,51,0.15); color: var(--color-accent1);">
                        {{ ucfirst(str_replace('-', ' ', $homeVisit->visit_type)) }}
                    </span>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider mb-2" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Scheduled For</div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" style="color: rgba(255,255,255,0.4);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"></path>
                        </svg>
                        <div class="text-sm" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $homeVisit->schedule?->format('F d, Y g:i A') }}</div>
                    </div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider mb-2" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Purpose</div>
                    <div class="text-sm" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $homeVisit->purpose }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider mb-2" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Assigned Staff</div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" style="color: rgba(255,255,255,0.4);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
                        </svg>
                        <div class="text-sm" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $homeVisit->assignedStaff?->display_name ?? 'Unassigned' }}</div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Notes</label>
                <textarea name="notes"
                    class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                    rows="6"
                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white; resize: vertical;"
                    placeholder="Add visit notes" maxlength="500">{{ old('notes', $homeVisit->notes ?? '') }}</textarea>
                <x-forms.errors name="notes" />
            </div>
        </div>
    </div>
@endcan