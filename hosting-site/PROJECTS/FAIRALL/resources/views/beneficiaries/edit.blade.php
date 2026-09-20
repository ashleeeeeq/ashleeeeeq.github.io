<x-dashboardlayout name="{{ $name }}" title="Edit Beneficiary">
    <div class="mb-8">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight"
                    style="font-family: var(--font-header1); color: var(--color-white);">
                    Edit Beneficiary
                </h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                    Update beneficiary information
                </p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
        </div>

        <form method="POST" action="/beneficiaries/{{ $beneficiary->id }}" novalidate>
            @csrf
            @method('PUT')
            <input type="hidden" name="program_id" value="{{ $programId }}" />

            <!-- Program Information -->
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                    style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white text-sm sm:text-base"
                                style="font-family: var(--font-header1);">Program Information</h3>
                            <p class="text-xs" style="color: rgba(255,255,255,0.5);">The program this beneficiary is
                                enrolled in</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Program</label>
                        <input type="text"
                            class="w-full px-4 py-2.5 text-primary1 rounded-xl border cursor-not-allowed"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                            value="{{ $selectedProgram->program_name }}" readonly>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl mt-6"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                    style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white text-sm sm:text-base"
                                style="font-family: var(--font-header1);">Personal Information</h3>
                            <p class="text-xs" style="color: rgba(255,255,255,0.5);">Update the beneficiary's personal
                                details</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">First Name <span
                                    class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="text" name="first_name"
                                class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                                value="{{ old('first_name', $beneficiary->first_name) }}" placeholder="Enter first name"
                                required>
                            <x-forms.errors name="first_name" />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Middle
                                Name</label>
                            <input type="text" name="middle_name"
                                class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                                value="{{ old('middle_name', $beneficiary->middle_name) }}"
                                placeholder="Enter middle name">
                            <x-forms.errors name="middle_name" />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Last Name <span
                                    class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="text" name="last_name"
                                class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                                value="{{ old('last_name', $beneficiary->last_name) }}" placeholder="Enter last name"
                                required>
                            <x-forms.errors name="last_name" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Extension (Jr,
                                Sr)</label>
                            <input type="text" name="name_extension"
                                class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                                value="{{ old('name_extension', $beneficiary->name_extension) }}"
                                placeholder="e.g., Jr, Sr">
                            <x-forms.errors name="name_extension" />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Birth Date <span
                                    class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="date" name="birth_date"
                                class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                                value="{{ old('birth_date', optional($beneficiary->birth_date)->format('Y-m-d')) }}"
                                required>
                            <x-forms.errors name="birth_date" />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Sex <span
                                    class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <div class="flex gap-4 pt-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="sex" value="male"
                                        class="w-4 h-4 rounded border focus:ring-2 focus:ring-accent1 transition-all duration-200 cursor-pointer"
                                        style="accent-color: var(--color-accent1); border-color: rgba(255,255,255,0.2); background-color: rgba(255,255,255,0.05);"
                                        @checked(old('sex', $beneficiary->sex) === 'male') required>
                                    <span class="text-sm"
                                        style="color: white; font-family: var(--font-body1);">Male</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="sex" value="female"
                                        class="w-4 h-4 rounded border focus:ring-2 focus:ring-accent1 transition-all duration-200 cursor-pointer"
                                        style="accent-color: var(--color-accent1); border-color: rgba(255,255,255,0.2); background-color: rgba(255,255,255,0.05);"
                                        @checked(old('sex', $beneficiary->sex) === 'female')>
                                    <span class="text-sm"
                                        style="color: white; font-family: var(--font-body1);">Female</span>
                                </label>
                            </div>
                            <x-forms.errors name="sex" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl mt-6"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                    style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white text-sm sm:text-base"
                                style="font-family: var(--font-header1);">Contact Information</h3>
                            <p class="text-xs" style="color: rgba(255,255,255,0.5);">Update the beneficiary's contact
                                details</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Email</label>
                                <input type="email" name="email"
                                    class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                                    value="{{ old('email', $beneficiary->user?->email) }}"
                                    placeholder="beneficiary@example.com">
                                <x-forms.errors name="email" />
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Contact
                                    Number
                                    <span class="text-xs font-normal"
                                        style="color: var(--color-danger);">*</span></label>
                                <x-forms.phone-input name="contact_number" value="{{ old('contact_number', $beneficiary->contact_number) }}"
                                    :required="true" />
                            </div>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Address Line
                                <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="text" name="address_line"
                                class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                                value="{{ old('address_line', $addressParts['address_line'] ?? '') }}"
                                placeholder="Enter street address" required>
                            <x-forms.errors name="address_line" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Country <span
                                        class="text-xs font-normal"
                                        style="color: var(--color-danger);">*</span></label>
                                <input type="text" name="country"
                                    class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                                    value="{{ old('country', $addressParts['country'] ?? '') }}"
                                    placeholder="Enter country" required>
                                <x-forms.errors name="country" />
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">State /
                                    Province <span class="text-xs font-normal"
                                        style="color: var(--color-danger);">*</span></label>
                                <input type="text" name="province"
                                    class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                                    value="{{ old('province', $addressParts['province'] ?? '') }}"
                                    placeholder="Enter state/province" required>
                                <x-forms.errors name="province" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">City <span
                                        class="text-xs font-normal"
                                        style="color: var(--color-danger);">*</span></label>
                                <input type="text" name="city"
                                    class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                                    value="{{ old('city', $addressParts['city'] ?? '') }}" placeholder="Enter city"
                                    required>
                                <x-forms.errors name="city" />
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Zip <span
                                        class="text-xs font-normal"
                                        style="color: var(--color-danger);">*</span></label>
                                <input type="text" name="zip"
                                    class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                                    value="{{ old('zip', $addressParts['zip'] ?? '') }}" placeholder="Enter zip code"
                                    required>
                                <x-forms.errors name="zip" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl mt-6"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                    style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white text-sm sm:text-base"
                                style="font-family: var(--font-header1);">Account Information</h3>
                            <p class="text-xs" style="color: rgba(255,255,255,0.5);">Update the user's login
                                credentials</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">New Password
                                    <span class="text-white/40 text-xs font-normal">(optional)</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-5 h-5" style="color: var(--color-primary1);" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                            </path>
                                        </svg>
                                    </div>
                                    <input type="password" name="password" id="password"
                                        class="w-full pl-10 pr-12 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                                        placeholder="Leave blank to keep current">
                                </div>
                                <x-forms.errors name="password" />
                                <ul id="password-requirements" class="mt-2 space-y-0.5"></ul>
                                <script>document.addEventListener('DOMContentLoaded',function(){initPasswordRequirements('password','password-requirements')});</script>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Confirm New
                                    Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-5 h-5" style="color: var(--color-primary1);" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                            </path>
                                        </svg>
                                    </div>
                                    <input type="password" name="password_confirmation" id="confirmPassword"
                                        class="w-full pl-10 pr-12 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                                        placeholder="Confirm new password">
                                </div>
                                <ul id="confirm-password-requirements" class="mt-2 space-y-0.5"></ul>
                                <script>document.addEventListener('DOMContentLoaded',function(){initPasswordMatch('password','confirmPassword','confirm-password-requirements')});</script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Information -->
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl mt-6"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                    style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white text-sm sm:text-base"
                                style="font-family: var(--font-header1);">Other Information</h3>
                            <p class="text-xs" style="color: rgba(255,255,255,0.5);">Additional beneficiary details
                            </p>
                        </div>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="flex flex-wrap gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="form_given" value="1"
                                class="w-4 h-4 rounded border focus:ring-2 focus:ring-accent1 transition-all duration-200 cursor-pointer"
                                style="accent-color: var(--color-accent1); border-color: rgba(255,255,255,0.2); background-color: rgba(255,255,255,0.05);"
                                @checked((bool) old('form_given', $beneficiary->form_given))>
                            <span class="text-sm" style="color: white; font-family: var(--font-body1);">Form
                                Given</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="with_disability" value="1"
                                class="w-4 h-4 rounded border focus:ring-2 focus:ring-accent1 transition-all duration-200 cursor-pointer"
                                style="accent-color: var(--color-accent1); border-color: rgba(255,255,255,0.2); background-color: rgba(255,255,255,0.05);"
                                @checked((bool) old('with_disability', $beneficiary->with_disability))>
                            <span class="text-sm" style="color: white; font-family: var(--font-body1);">With
                                Disability</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-6 mt-6 border-t"
                style="border-color: rgba(255,255,255,0.1);">
                <a href="/beneficiaries"
                    class="px-6 py-3 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] text-center inline-flex items-center justify-center gap-2 group"
                    style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                    onmouseover="this.style.backgroundColor='rgba(248,113,113,0.1)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)'"
                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-danger)'; this.style.borderColor='var(--color-danger)'">
                    <svg class="w-4 h-4 transition-all duration-300 group-hover:rotate-90" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-3 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center justify-center gap-2 group"
                    style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                    onmouseover="this.style.filter='brightness(0.95)'" onmouseout="this.style.filter='brightness(1)'">
                    <svg class="w-4 h-4 transition-all duration-300 group-hover:translate-x-1" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25">
                        </path>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-dashboardlayout>
