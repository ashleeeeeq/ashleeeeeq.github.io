@php
    $intake = $intake ?? [];
    $educationLevels = ['pre-school', 'elementary', 'high_school', 'shs', 'college', 'post-grad'];
    $gradeLevels = [
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        '10',
        '11',
        '12',
        '1st Year',
        '2nd Year',
        '3rd Year',
        '4th Year',
        '5th Year',
    ];
@endphp

<div class="space-y-6">
    <!-- Personal Details Section -->
    <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
        style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
            style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                    style="background-color: rgba(255,204,51,0.15);">
                    <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Personal Details</h3>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Age</label>
                        <input type="number" name="intake[age]"
                            class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                            placeholder="e.g., 15" value="{{ $intake['age'] ?? '' }}" min="1">
                        <x-forms.errors name="intake.age" />
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Civil Status</label>
                        <div class="civil-status-wrapper" data-field-name="intake[civil_status]">
                            <select name="intake[civil_status]"
                                class="civil-status-select mb-2 w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;">
                                <option value="" @selected(empty($intake['civil_status']))
                                    style="color: var(--color-primary1);">Select civil status</option>
                                <option value="Single" @selected(($intake['civil_status'] ?? '') === 'Single')
                                    style="color: var(--color-primary1);">Single</option>
                                <option value="Married" @selected(($intake['civil_status'] ?? '') === 'Married')
                                    style="color: var(--color-primary1);">Married</option>
                                <option value="Divorced" @selected(($intake['civil_status'] ?? '') === 'Divorced')
                                    style="color: var(--color-primary1);">Divorced</option>
                                <option value="__other__" style="color: var(--color-primary1);">Others: Specify</option>
                            </select>
                            <div
                                class="civil-status-other {{ !in_array($intake['civil_status'] ?? '', ['', 'Single', 'Married', 'Divorced']) && !empty($intake['civil_status']) ? '' : 'hidden' }}">
                                <input type="text"
                                    class="civil-status-text w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                    placeholder="Please specify" value="{{ $intake['civil_status'] ?? '' }}" />
                            </div>
                        </div>
                        <x-forms.errors name="intake.civil_status" />
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Place of Birth</label>
                    <input type="text" name="intake[place_of_birth]"
                        class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                        placeholder="City, Province" value="{{ $intake['place_of_birth'] ?? '' }}">
                    <x-forms.errors name="intake.place_of_birth" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Number of
                            Siblings</label>
                        <input type="number" name="intake[number_of_siblings]"
                            class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                            placeholder="e.g., 3" value="{{ $intake['number_of_siblings'] ?? '' }}" min="0">
                        <x-forms.errors name="intake.number_of_siblings" />
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Age of Older
                            Sibling</label>
                        <input type="number" name="intake[older_sibling_age]"
                            class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                            placeholder="e.g., 20" value="{{ $intake['older_sibling_age'] ?? '' }}" min="0">
                        <x-forms.errors name="intake.older_sibling_age" />
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Age of Younger
                            Sibling</label>
                        <input type="number" name="intake[younger_sibling_age]"
                            class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                            placeholder="e.g., 10" value="{{ $intake['younger_sibling_age'] ?? '' }}" min="0">
                        <x-forms.errors name="intake.younger_sibling_age" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Education Section -->
    <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
        style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
            style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                    style="background-color: rgba(255,204,51,0.15);">
                    <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Education
                        Information</h3>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Highest Educational
                            Attainment</label>
                        <div class="relative">
                            <select name="intake[highest_education]"
                                class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;">
                                <option value="" style="color: var(--color-primary1);" disabled
                                    {{ empty($intake['highest_education']) ? 'selected' : '' }}>Select an option
                                </option>
                                @foreach ($educationLevels as $level)
                                    <option value="{{ $level }}" style="color: var(--color-primary1);"
                                        @selected(($intake['highest_education'] ?? '') === $level)>
                                        {{ ucwords(str_replace('_', ' ', $level)) }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                </svg>
                            </div>
                        </div>
                        <x-forms.errors name="intake.highest_education" />
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Grade Level</label>
                        <div class="relative">
                            <select name="intake[grade_level]"
                                class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;">
                                <option value="" style="color: var(--color-primary1);" disabled
                                    {{ empty($intake['grade_level']) ? 'selected' : '' }}>Select an option</option>
                                @foreach ($gradeLevels as $level)
                                    <option value="{{ $level }}" style="color: var(--color-primary1);"
                                        @selected(($intake['grade_level'] ?? '') === $level)>{{ $level }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                </svg>
                            </div>
                        </div>
                        <x-forms.errors name="intake.grade_level" />
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">School Name</label>
                    <input type="text" name="intake[school_name]"
                        class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                        placeholder="Name of school" value="{{ $intake['school_name'] ?? '' }}">
                    <x-forms.errors name="intake.school_name" />
                </div>
            </div>
        </div>
    </div>

    <!-- Scholarship Section -->
    <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
        style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
            style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                    style="background-color: rgba(255,204,51,0.15);">
                    <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Scholarship
                        Information</h3>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div>
                    <label class="block mb-2 text-sm font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Mayroon ka bang
                        scholarship o education sponsorship mula sa ibang mga organisasyon?</label>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="intake[other_scholarship]" value="1"
                                class="w-4 h-4 border-gray-300 accent-accent1" @checked((bool) ($intake['other_scholarship'] ?? false)) />
                            <span class="text-sm" style="color: rgba(255,255,255,0.7);">Yes</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="intake[other_scholarship]" value="0"
                                class="w-4 h-4 border-gray-300 accent-accent1" @checked(!(bool) ($intake['other_scholarship'] ?? false)) />
                            <span class="text-sm" style="color: rgba(255,255,255,0.7);">No</span>
                        </label>
                    </div>
                    <x-forms.errors name="intake.other_scholarship" />
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Kung "Opo/ Yes," anong
                        organisasyon ang kasalukuyang nagbibigay sa iyo ng scholarship?</label>
                    <textarea name="intake[scholarship_org_question]"
                        class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;"
                        rows="3" placeholder="Enter organization name(s)">{{ $intake['scholarship_org_question'] ?? '' }}</textarea>
                    <x-forms.errors name="intake.scholarship_org_question" />
                </div>
            </div>
        </div>
    </div>

    <!-- Essay Questions Section -->
    <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
        style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
            style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                    style="background-color: rgba(255,204,51,0.15);">
                    <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Essay Questions
                    </h3>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div>
                    <label class="block mb-2 text-sm font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Ikaw ba ay isang masipag
                        na estudyante? Bakit mo nasabi yun?</label>
                    <textarea name="intake[hardworking_question]"
                        class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;"
                        rows="4" placeholder="Ilahad ang iyong sagot dito...">{{ $intake['hardworking_question'] ?? '' }}</textarea>
                    <x-forms.errors name="intake.hardworking_question" />
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Ano ang pangarap mo sa
                        buhay?</label>
                    <textarea name="intake[dream_question]"
                        class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;"
                        rows="4" placeholder="Ilahad ang iyong pangarap...">{{ $intake['dream_question'] ?? '' }}</textarea>
                    <x-forms.errors name="intake.dream_question" />
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Bakit mo gustong maging
                        Fairplay Scholar?</label>
                    <textarea name="intake[scholarship_question]"
                        class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;"
                        rows="4" placeholder="Ilahad ang iyong dahilan...">{{ $intake['scholarship_question'] ?? '' }}</textarea>
                    <x-forms.errors name="intake.scholarship_question" />
                </div>
            </div>
        </div>
    </div>
</div>

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
