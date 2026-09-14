<x-dashboardlayout name="{{ $name }}" title="Add Guardian">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Add
                    Guardian</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Beneficiary: <span
                        class="font-semibold text-white">{{ $beneficiary->display_name }}</span></p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        <form method="POST" action="/beneficiaries/{{ $beneficiary->id }}/guardians">
            @csrf

            <!-- Guardian Information Card -->
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                    style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
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
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Guardian
                                Information</h2>
                            <p class="text-xs text-white/50">Enter the guardian's personal and contact details</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Guardian Type -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Guardian Type
                                <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <div class="relative">
                                <select name="guardian_type"
                                    class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;"
                                    required>
                                    <option value="" disabled @selected(empty(old('guardian_type')))
                                        style="color: var(--color-primary1);">Select type</option>
                                    <option value="mother" @selected(old('guardian_type') === 'mother')
                                        style="color: var(--color-primary1);">Mother</option>
                                    <option value="father" @selected(old('guardian_type') === 'father')
                                        style="color: var(--color-primary1);">Father</option>
                                    <option value="guardian" @selected(old('guardian_type') === 'guardian')
                                        style="color: var(--color-primary1);">Guardian</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                    </svg>
                                </div>
                            </div>
                            <x-forms.errors name="guardian_type" />
                        </div>

                        <!-- First, Middle, and Last Name -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">First Name
                                    <span class="text-xs font-normal"
                                        style="color: var(--color-danger);">*</span></label>
                                <input type="text" name="first_name"
                                    class="w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                                    value="{{ old('first_name') }}" placeholder="Enter first name" required>
                                <x-forms.errors name="first_name" />
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Middle
                                    Name</label>
                                <input type="text" name="middle_name"
                                    class="w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                                    value="{{ old('middle_name') }}" placeholder="Enter middle name">
                                <x-forms.errors name="middle_name" />
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Last Name
                                    <span class="text-xs font-normal"
                                        style="color: var(--color-danger);">*</span></label>
                                <input type="text" name="last_name"
                                    class="w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                                    value="{{ old('last_name') }}" placeholder="Enter last name" required>
                                <x-forms.errors name="last_name" />
                            </div>
                        </div>

                        <!-- Sex -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Sex</label>
                            <div class="flex gap-4 pt-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="sex" value="male" class="w-4 h-4 accent-accent1"
                                        @checked(old('sex') === 'male')>
                                    <span class="text-sm" style="color: white;">Male</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="sex" value="female"
                                        class="w-4 h-4 accent-accent1" @checked(old('sex') === 'female')>
                                    <span class="text-sm" style="color: white;">Female</span>
                                </label>
                            </div>
                            <x-forms.errors name="sex" />
                        </div>

                        <!-- Birth Date and Place of Birth -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Birth
                                    Date</label>
                                <input type="date" name="birth_date"
                                    class="w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                                    value="{{ old('birth_date') }}">
                                <x-forms.errors name="birth_date" />
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Place of
                                    Birth</label>
                                <input type="text" name="place_of_birth"
                                    class="w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                                    value="{{ old('place_of_birth') }}" placeholder="City, Province">
                                <x-forms.errors name="place_of_birth" />
                            </div>
                        </div>

                        <!-- Civil Status -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Civil
                                Status</label>
                            <div class="civil-status-wrapper" data-field-name="civil_status">
                                <select
                                    class="civil-status-select mb-2 w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;">
                                    <option value="" style="color: var(--color-primary1);">Select civil status
                                    </option>
                                    <option value="Single" style="color: var(--color-primary1);"
                                        @selected(old('civil_status') === 'Single')>Single</option>
                                    <option value="Married" style="color: var(--color-primary1);"
                                        @selected(old('civil_status') === 'Married')>Married</option>
                                    <option value="Divorced" style="color: var(--color-primary1);"
                                        @selected(old('civil_status') === 'Divorced')>Divorced</option>
                                    <option value="__other__" style="color: var(--color-primary1);" class="mt-2">
                                        Others: Specify</option>
                                </select>
                                <div
                                    class="civil-status-other {{ !in_array(old('civil_status'), ['', 'Single', 'Married', 'Divorced']) && old('civil_status') ? '' : 'hidden' }}">
                                    <input type="text"
                                        class="civil-status-text w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                        placeholder="Please specify" value="{{ old('civil_status') }}" />
                                </div>
                            </div>
                            <x-forms.errors name="civil_status" />
                        </div>

                        <!-- Contact Number -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Contact
                                Number
                                <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <x-forms.phone-input name="contact_number" value="{{ old('contact_number') }}"
                                :required="true" />
                        </div>

                        <!-- Highest Education -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Highest
                                Education</label>
                            <div class="relative">
                                <select name="highest_education"
                                    class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;">
                                    <option value="" disabled @selected(empty(old('highest_education')))
                                        style="color: var(--color-primary1);">Select level</option>
                                    <option value="elementary" @selected(old('highest_education') === 'elementary')
                                        style="color: var(--color-primary1);">Elementary</option>
                                    <option value="high_school" @selected(old('highest_education') === 'high_school')
                                        style="color: var(--color-primary1);">High School</option>
                                    <option value="shs" @selected(old('highest_education') === 'shs')
                                        style="color: var(--color-primary1);">Senior High</option>
                                    <option value="college" @selected(old('highest_education') === 'college')
                                        style="color: var(--color-primary1);">College</option>
                                    <option value="post-grad" @selected(old('highest_education') === 'post-grad')
                                        style="color: var(--color-primary1);">Post-Grad</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                    </svg>
                                </div>
                            </div>
                            <x-forms.errors name="highest_education" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Occupation -->
                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Occupation</label>
                                <input type="text" name="job"
                                    class="w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                                    value="{{ old('job') }}" placeholder="Enter occupation">
                                <x-forms.errors name="job" />
                            </div>

                            <!-- Estimated Salary -->
                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Estimated
                                    Salary</label>
                                <input type="text" name="estimated_salary"
                                    class="w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                                    value="{{ old('estimated_salary') }}" placeholder="e.g., ₱25,000 - ₱30,000">
                                <x-forms.errors name="estimated_salary" />
                            </div>
                        </div>

                        <!-- Living Status -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Living
                                Status</label>
                            <div class="flex gap-6">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="deceased" value="0"
                                        class="w-4 h-4 border-gray-300 accent-accent1" @checked(!(bool) old('deceased')) />
                                    <span class="text-sm" style="color: rgba(255,255,255,0.7);">Alive</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="deceased" value="1"
                                        class="w-4 h-4 border-gray-300 accent-accent1" @checked((bool) old('deceased')) />
                                    <span class="text-sm" style="color: rgba(255,255,255,0.7);">Deceased</span>
                                </label>
                            </div>
                            <x-forms.errors name="deceased" />
                        </div>

                        <!-- Address -->
                        <div class="border-t border-white/10 pt-4 mt-4">
                            <p class="text-sm font-semibold mb-3"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Address</p>
                            <div class="mb-3">
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Address
                                    Line</label>
                                <input type="text" name="address_line"
                                    class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                    placeholder="Street address, Purok, Barangay" value="{{ old('address_line') }}">
                                <x-forms.errors name="address_line" />
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-2 text-sm font-semibold"
                                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Country</label>
                                    <input type="text" name="country"
                                        class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                        placeholder="Country" value="{{ old('country') }}">
                                    <x-forms.errors name="country" />
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-semibold"
                                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">State /
                                        Province</label>
                                    <input type="text" name="province"
                                        class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                        placeholder="State or province" value="{{ old('province') }}">
                                    <x-forms.errors name="province" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                                <div>
                                    <label class="block mb-2 text-sm font-semibold"
                                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">City</label>
                                    <input type="text" name="city"
                                        class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                        placeholder="City" value="{{ old('city') }}">
                                    <x-forms.errors name="city" />
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-semibold"
                                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Zip</label>
                                    <input type="text" name="zip"
                                        class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                        placeholder="ZIP code" value="{{ old('zip') }}">
                                    <x-forms.errors name="zip" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 pt-6 mt-6 border-t" style="border-color: rgba(255,255,255,0.1);">
                <a href="/beneficiaries/{{ $beneficiary->id }}/guardians"
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
                    Add Guardian
                </button>
            </div>
        </form>
    </div>

    <script>
        function initGuardianCivilStatus(wrapper) {
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

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.civil-status-wrapper').forEach(initGuardianCivilStatus);
        });
    </script>
</x-dashboardlayout>
