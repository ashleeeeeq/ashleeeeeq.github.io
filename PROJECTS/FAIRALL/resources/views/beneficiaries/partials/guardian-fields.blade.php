@php
    $guardianEntries = $guardianEntries ?? [];
    $guardianEntries = array_values($guardianEntries);
    $guardianNextIndex = $guardianNextIndex ?? count($guardianEntries);
@endphp

<div class="space-y-6" id="guardians-container" data-next-index="{{ $guardianNextIndex }}">
    @foreach ($guardianEntries as $index => $guardian)
        <div class="rounded-xl overflow-hidden guardian-block" style="border: 1px solid rgba(255,255,255,0.1);">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Guardian
                            {{ $index + 1 }}</h3>
                    </div>
                    @if ($index > 0)
                        <button type="button"
                            class="remove-guardian-btn p-1.5 rounded-lg transition-all hover:bg-red-500/20"
                            title="Remove Guardian">
                            <svg class="w-4 h-4" style="color: #f87171;" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @if (!empty($guardian['id']))
                        <input type="hidden" name="guardians[{{ $index }}][id]" value="{{ $guardian['id'] }}" />
                    @endif

                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Guardian Type <span
                                class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                        <div class="relative">
                            <select name="guardians[{{ $index }}][guardian_type]"
                                class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;"
                                required>
                                <option value="" style="color: var(--color-primary1);"
                                    @selected(empty($guardian['guardian_type']))>Select type</option>
                                <option value="mother" style="color: var(--color-primary1);"
                                    @selected(($guardian['guardian_type'] ?? '') === 'mother')>Mother</option>
                                <option value="father" style="color: var(--color-primary1);"
                                    @selected(($guardian['guardian_type'] ?? '') === 'father')>Father</option>
                                <option value="guardian" style="color: var(--color-primary1);"
                                    @selected(($guardian['guardian_type'] ?? '') === 'guardian')>Guardian</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                </svg>
                            </div>
                        </div>
                        <x-forms.errors name="guardians.{{ $index }}.guardian_type" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">First Name <span
                                    class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="text" name="guardians[{{ $index }}][first_name]"
                                class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                placeholder="Enter first name" value="{{ $guardian['first_name'] ?? '' }}" required />
                            <x-forms.errors name="guardians.{{ $index }}.first_name" />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Middle
                                Name</label>
                            <input type="text" name="guardians[{{ $index }}][middle_name]"
                                class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                placeholder="Enter middle name" value="{{ $guardian['middle_name'] ?? '' }}" />
                            <x-forms.errors name="guardians.{{ $index }}.middle_name" />
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Last Name <span
                                    class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="text" name="guardians[{{ $index }}][last_name]"
                                class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                placeholder="Enter last name" value="{{ $guardian['last_name'] ?? '' }}" required />
                            <x-forms.errors name="guardians.{{ $index }}.last_name" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Civil
                                Status</label>
                            <div class="civil-status-wrapper"
                                data-field-name="guardians[{{ $index }}][civil_status]">
                                <select
                                    class="civil-status-select mb-2 w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;">
                                    <option value="" style="color: var(--color-primary1);">Select civil status
                                    </option>
                                    <option value="Single" style="color: var(--color-primary1);"
                                        @selected(($guardian['civil_status'] ?? '') === 'Single')>Single</option>
                                    <option value="Married" style="color: var(--color-primary1);"
                                        @selected(($guardian['civil_status'] ?? '') === 'Married')>Married</option>
                                    <option value="Divorced" style="color: var(--color-primary1);"
                                        @selected(($guardian['civil_status'] ?? '') === 'Divorced')>Divorced</option>
                                    <option value="__other__" style="color: var(--color-primary1);">Others: Specify
                                    </option>
                                </select>
                                <div
                                    class="civil-status-other {{ !in_array($guardian['civil_status'] ?? '', ['', 'Single', 'Married', 'Divorced']) && !empty($guardian['civil_status']) ? '' : 'hidden' }} mt-4">
                                    <input type="text"
                                        class="civil-status-text w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                        placeholder="Please specify" value="{{ $guardian['civil_status'] ?? '' }}" />
                                </div>
                            </div>
                            <x-forms.errors name="guardians.{{ $index }}.civil_status" />
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Sex</label>
                            <div class="flex gap-4 pt-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="guardians[{{ $index }}][sex]" value="male"
                                        class="w-4 h-4 accent-accent1" @checked(($guardian['sex'] ?? '') === 'male')>
                                    <span class="text-sm" style="color: white;">Male</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="guardians[{{ $index }}][sex]" value="female"
                                        class="w-4 h-4 accent-accent1" @checked(($guardian['sex'] ?? '') === 'female')>
                                    <span class="text-sm" style="color: white;">Female</span>
                                </label>
                            </div>
                            <x-forms.errors name="guardians.{{ $index }}.sex" />
                        </div>

                    </div>

                    <div class="grid grid-cols-2 max-xs:grid-cols-1 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Birth
                                Date</label>
                            <input type="date" name="guardians[{{ $index }}][birth_date]"
                                class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                value="{{ $guardian['birth_date'] ?? '' }}" />
                            <x-forms.errors name="guardians.{{ $index }}.birth_date" />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Place of
                                Birth</label>
                            <input type="text" name="guardians[{{ $index }}][place_of_birth]"
                                class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                placeholder="City, Province" value="{{ $guardian['place_of_birth'] ?? '' }}" />
                            <x-forms.errors name="guardians.{{ $index }}.place_of_birth" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 max-xs:grid-cols-1 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Contact
                                Number
                                <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <x-forms.phone-input name="guardians[{{ $index }}][contact_number]"
                                value="{{ $guardian['contact_number'] ?? '' }}" :required="true" />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Highest
                                Educational Attainment</label>
                            <div class="relative">
                                <select name="guardians[{{ $index }}][highest_education]"
                                    class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;">
                                    <option value="" style="color: var(--color-primary1);"
                                        @selected(empty($guardian['highest_education']))>Select level</option>
                                    <option value="elementary" style="color: var(--color-primary1);"
                                        @selected(($guardian['highest_education'] ?? '') === 'elementary')>Elementary</option>
                                    <option value="high_school" style="color: var(--color-primary1);"
                                        @selected(($guardian['highest_education'] ?? '') === 'high_school')>High School</option>
                                    <option value="shs" style="color: var(--color-primary1);"
                                        @selected(($guardian['highest_education'] ?? '') === 'shs')>Senior High</option>
                                    <option value="college" style="color: var(--color-primary1);"
                                        @selected(($guardian['highest_education'] ?? '') === 'college')>College</option>
                                    <option value="post-grad" style="color: var(--color-primary1);"
                                        @selected(($guardian['highest_education'] ?? '') === 'post-grad')>Post-Grad</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                    </svg>
                                </div>
                            </div>
                            <x-forms.errors name="guardians.{{ $index }}.highest_education" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Occupation</label>
                            <input type="text" name="guardians[{{ $index }}][job]"
                                class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                placeholder="Job title" value="{{ $guardian['job'] ?? '' }}" />
                            <x-forms.errors name="guardians.{{ $index }}.job" />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Estimated
                                Salary</label>
                            <input type="number" step="1"
                                name="guardians[{{ $index }}][estimated_salary]"
                                class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                placeholder="e.g., 20000" value="{{ $guardian['estimated_salary'] ?? '' }}" />
                            <x-forms.errors name="guardians.{{ $index }}.estimated_salary" />
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Living
                                Status</label>
                            <div class="flex gap-6">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="guardians[{{ $index }}][deceased]"
                                        value="0" class="w-4 h-4 border-gray-300 accent-accent1"
                                        @checked(!(bool) ($guardian['deceased'] ?? false)) />
                                    <span class="text-sm" style="color: rgba(255,255,255,0.7);">Alive</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="guardians[{{ $index }}][deceased]"
                                        value="1" class="w-4 h-4 border-gray-300 accent-accent1"
                                        @checked((bool) ($guardian['deceased'] ?? false)) />
                                    <span class="text-sm" style="color: rgba(255,255,255,0.7);">Deceased</span>
                                </label>
                            </div>
                            <x-forms.errors name="guardians.{{ $index }}.deceased" />
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="border-t border-white/10 pt-4 mt-4">
                        <p class="text-sm font-semibold mb-3"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Address</p>
                        <div class="mb-3">
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Address
                                Line</label>
                            <input type="text" name="guardians[{{ $index }}][address_line]"
                                class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                placeholder="Street address, Purok, Barangay"
                                value="{{ $guardian['address_line'] ?? '' }}" />
                            <x-forms.errors name="guardians.{{ $index }}.address_line" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Country</label>
                                <input type="text" name="guardians[{{ $index }}][country]"
                                    class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                    placeholder="Country" value="{{ $guardian['country'] ?? '' }}" />
                                <x-forms.errors name="guardians.{{ $index }}.country" />
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">State /
                                    Province</label>
                                <input type="text" name="guardians[{{ $index }}][province]"
                                    class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                    placeholder="State or province" value="{{ $guardian['province'] ?? '' }}" />
                                <x-forms.errors name="guardians.{{ $index }}.province" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">City</label>
                                <input type="text" name="guardians[{{ $index }}][city]"
                                    class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                    placeholder="City" value="{{ $guardian['city'] ?? '' }}" />
                                <x-forms.errors name="guardians.{{ $index }}.city" />
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Zip</label>
                                <input type="text" name="guardians[{{ $index }}][zip]"
                                    class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                    placeholder="ZIP code" value="{{ $guardian['zip'] ?? '' }}" />
                                <x-forms.errors name="guardians.{{ $index }}.zip" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="mt-4 ml-1">
    <button type="button" id="add-guardian"
        class="inline-flex items-center gap-2 px-4 py-2 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02]"
        style="font-family: var(--font-body1); background-color: transparent; color: var(--color-accent1); border: 1px solid var(--color-accent1);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add Guardian
    </button>
</div>

<template id="guardian-template">
    <div class="rounded-xl overflow-hidden guardian-block" style="border: 1px solid rgba(255,255,255,0.1);">
        <div class="px-6 py-4 border-b"
            style="border-color: rgba(255,255,255,0.1); background-color: rgba(255,255,255,0.03);">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                        style="background-color: rgba(255,204,51,0.15);">
                        <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Guardian __NUMBER__
                    </h3>
                </div>
                <button type="button" class="remove-guardian-btn p-1.5 rounded-lg transition-all hover:bg-red-500/20"
                    title="Remove Guardian">
                    <svg class="w-4 h-4" style="color: #f87171;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div>
                    <label class="block mb-2 text-sm font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Guardian Type <span
                            class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                    <div class="relative">
                        <select name="guardians[__INDEX__][guardian_type]"
                            class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;"
                            required>
                            <option value="" style="color: var(--color-primary1);" disabled selected>Select type
                            </option>
                            <option value="mother" style="color: var(--color-primary1);">Mother</option>
                            <option value="father" style="color: var(--color-primary1);">Father</option>
                            <option value="guardian" style="color: var(--color-primary1);">Guardian</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">First Name <span
                                class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                        <input type="text" name="guardians[__INDEX__][first_name]"
                            class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                            placeholder="Enter first name" required />
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Middle Name</label>
                        <input type="text" name="guardians[__INDEX__][middle_name]"
                            class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                            placeholder="Enter middle name" />
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Last Name <span
                                class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                        <input type="text" name="guardians[__INDEX__][last_name]"
                            class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                            placeholder="Enter last name" required />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Civil Status</label>
                        <div class="civil-status-wrapper" data-field-name="guardians[__INDEX__][civil_status]">
                            <select
                                class="civil-status-select mb-2 w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;">
                                <option value="" style="color: var(--color-primary1);" selected>Select civil
                                    status</option>
                                <option value="Single" style="color: var(--color-primary1);">Single</option>
                                <option value="Married" style="color: var(--color-primary1);">Married</option>
                                <option value="Divorced" style="color: var(--color-primary1);">Divorced</option>
                                <option value="__other__" style="color: var(--color-primary1);">Others: Specify
                                </option>
                            </select>
                            <div class="civil-status-other hidden">
                                <input type="text"
                                    class="civil-status-text w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                    placeholder="Please specify" />
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Sex</label>
                        <div class="flex gap-4 pt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="guardians[__INDEX__][sex]" value="male"
                                    class="w-4 h-4 accent-accent1">
                                <span class="text-sm" style="color: white;">Male</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="guardians[__INDEX__][sex]" value="female"
                                    class="w-4 h-4 accent-accent1">
                                <span class="text-sm" style="color: white;">Female</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Birth Date</label>
                        <input type="date" name="guardians[__INDEX__][birth_date]"
                            class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);" />
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Place of
                            Birth</label>
                        <input type="text" name="guardians[__INDEX__][place_of_birth]"
                            class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                            placeholder="City, Province" />
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Contact
                            Number
                            <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                        <x-forms.phone-input name="guardians[__INDEX__][contact_number]" :required="true" />
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Highest
                            Education</label>
                        <div class="relative">
                            <select name="guardians[__INDEX__][highest_education]"
                                class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;">
                                <option value="" style="color: var(--color-primary1);" selected>Select level
                                </option>
                                <option value="elementary" style="color: var(--color-primary1);">Elementary</option>
                                <option value="high_school" style="color: var(--color-primary1);">High School</option>
                                <option value="shs" style="color: var(--color-primary1);">Senior High</option>
                                <option value="college" style="color: var(--color-primary1);">College</option>
                                <option value="post-grad" style="color: var(--color-primary1);">Post-Grad</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Occupation</label>
                        <input type="text" name="guardians[__INDEX__][job]"
                            class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                            placeholder="Job title" />
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Estimated
                            Salary</label>
                        <input type="number" step="1" name="guardians[__INDEX__][estimated_salary]"
                            class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                            placeholder="e.g., 20000" />
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Living
                            Status</label>
                        <div class="flex gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="guardians[__INDEX__][deceased]" value="0"
                                    class="w-4 h-4 border-gray-300 accent-accent1" />
                                <span class="text-sm" style="color: rgba(255,255,255,0.7);">Alive</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="guardians[__INDEX__][deceased]" value="1"
                                    class="w-4 h-4 border-gray-300 accent-accent1" />
                                <span class="text-sm" style="color: rgba(255,255,255,0.7);">Deceased</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="border-t border-white/10 pt-4 mt-4">
                    <p class="text-sm font-semibold mb-3"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Address</p>
                    <div class="mb-3">
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Address Line</label>
                        <input type="text" name="guardians[__INDEX__][address_line]"
                            class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                            placeholder="Street address, Purok, Barangay" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Country</label>
                            <input type="text" name="guardians[__INDEX__][country]"
                                class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                placeholder="Country" />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">State /
                                Province</label>
                            <input type="text" name="guardians[__INDEX__][province]"
                                class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                placeholder="State or province" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">City</label>
                            <input type="text" name="guardians[__INDEX__][city]"
                                class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                placeholder="City" />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Zip</label>
                            <input type="text" name="guardians[__INDEX__][zip]"
                                class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                placeholder="ZIP code" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    function initSingleCivilStatus(wrapper) {
        const select = wrapper.querySelector('.civil-status-select');
        const otherDiv = wrapper.querySelector('.civil-status-other');
        const textInput = otherDiv.querySelector('.civil-status-text');
        const fieldName = wrapper.dataset.fieldName;

        function sync() {
            select.name = fieldName;
            if (select.value === '__other__') {
                textInput.name = fieldName;
                otherDiv.classList.remove('hidden');
                textInput.focus();
            } else {
                textInput.removeAttribute('name');
                otherDiv.classList.add('hidden');
            }
        }

        const currentVal = textInput.value || select.value;
        if (currentVal !== '' && !['Single', 'Married', 'Divorced'].includes(currentVal)) {
            select.value = '__other__';
            textInput.value = currentVal;
        } else {
            select.value = currentVal;
        }
        sync();

        select.addEventListener('change', sync);
    }

    function initCivilStatus() {
        document.querySelectorAll('.civil-status-wrapper').forEach(initSingleCivilStatus);
    }

    document.addEventListener('DOMContentLoaded', initCivilStatus);
</script>
