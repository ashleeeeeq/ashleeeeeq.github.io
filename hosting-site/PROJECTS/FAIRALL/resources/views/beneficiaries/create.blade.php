<x-dashboardlayout name="{{ $name }}" title="Create Beneficiary">
    <div class="mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Create
                    Beneficiary</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Add a new beneficiary to the
                    program</p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        @if ($isAdministrator)
            <div class="mb-6 p-4 rounded-xl flex items-center justify-between gap-3 flex-wrap"
                style="background-color: rgba(255,204,51,0.1); border: 1px solid rgba(255,204,51,0.3);">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" style="color: var(--color-accent1);" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm" style="color: var(--color-accent1); font-family: var(--font-body1);">
                        Selected program: <strong>{{ $selectedProgram->program_name }}</strong>
                    </span>
                </div>
                <a href="/beneficiaries/create"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 hover:scale-[1.02]"
                    style="background-color: var(--color-accent1); color: var(--color-primary1);">Change</a>
            </div>
        @endif

        <form method="POST" action="/beneficiaries" class="requires-program-checkbox" enctype="multipart/form-data"
            novalidate>
            @csrf
            <input type="hidden" name="program_id" value="{{ $programId }}" />

            <!-- Tabs -->
            <div>
                <!-- Tab Headers -->
                @php
                    $errorKeys = collect($errors->keys());
                    $hasGuardianErrors = $errorKeys->contains(fn($k) => str_starts_with($k, 'guardians.'));
                    $hasIntakeErrors = $errorKeys->contains(fn($k) => str_starts_with($k, 'intake.'));
                    $hasStatusErrors = $errorKeys->contains(
                        fn($k) => in_array($k, ['status', 'start_date', 'end_date']) || str_starts_with($k, 'status'),
                    );
                @endphp

                <div class="flex max-md:flex-wrap max-md:justify-between gap-2 border-b border-white/20 mb-6">
                    <button type="button" onclick="switchTab('beneficiary')" id="beneficiaryTabBtn"
                        class="tab-btn-active px-5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 flex items-center gap-2 min-w-35"
                        style="font-family: var(--font-body1); color: var(--color-accent1); border-bottom: 2px solid var(--color-accent1); background-color: transparent;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Beneficiary
                    </button>
                    <button type="button" onclick="switchTab('guardian')" id="guardianTabBtn"
                        class="tab-btn-inactive px-5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 flex items-center gap-2 min-w-35"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.6); border-bottom: 2px solid transparent; background-color: transparent;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z">
                            </path>
                        </svg>
                        Guardian
                    </button>
                    @if ($isEducation)
                        <button type="button" onclick="switchTab('intake')" id="intakeTabBtn"
                            class="tab-btn-inactive px-5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 flex items-center gap-2 min-w-35"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.6); border-bottom: 2px solid transparent; background-color: transparent;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Intake Sheet
                        </button>
                    @endif
                    <button type="button" onclick="switchTab('status')" id="statusTabBtn"
                        class="tab-btn-inactive px-5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 flex items-center gap-2 min-w-35"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.6); border-bottom: 2px solid transparent; background-color: transparent;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z">
                            </path>
                        </svg>
                        Program Status
                    </button>
                </div>

                <!-- Verify Auto-fill Banner -->
                <div id="verifyAutofillBanner" class="hidden mb-6 px-5 py-4 rounded-xl flex items-center gap-3 border-2"
                    style="border-color: var(--color-accent1); background-color: rgba(255,204,51,0.12);">
                    <svg class="w-6 h-6 shrink-0" style="color: var(--color-accent1);" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                    </svg>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm"
                            style="color: var(--color-accent1); font-family: var(--font-header1);">
                            Verify Auto-fill
                        </p>
                        <p class="text-xs mt-0.5 text-white/70" style="font-family: var(--font-body1);">
                            <span id="verifyAutofillCount">0</span> field(s) were auto-filled from the uploaded
                            document.
                            <span class="font-medium">Fields with yellow borders need your review.</span>
                            Edit or click on a field to clear its highlight.
                        </p>
                    </div>
                    <button type="button"
                        onclick="document.getElementById('verifyAutofillBanner').classList.add('hidden')"
                        class="shrink-0 p-1.5 rounded-lg transition-colors hover:bg-white/10"
                        style="color: var(--color-accent1);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Tab Content: Beneficiary -->
                <div id="beneficiary-tab" class="tab-content" style="display: block;">
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
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">
                                        Program</h3>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block mb-2 text-sm font-semibold"
                                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Program</label>
                                    <input type="text"
                                        class="w-full px-4 py-2.5 rounded-lg border cursor-not-allowed"
                                        style="font-family: var(--font-body1); background-color: var(--color-white); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                        value="{{ $selectedProgram->program_name }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attach Document -->
                    <div class="rounded-xl overflow-hidden mt-6" style="border: 1px solid rgba(255,255,255,0.1);">
                        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                            style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                    style="background-color: rgba(255,204,51,0.15);">
                                    <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">
                                        Attach Document</h3>
                                    <p class="text-xs text-white/50">Upload a profile document — JPG/PNG for auto-fill,
                                        PDF accepted for regular upload</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <label class="block">
                                <div class="border-2 border-dashed border-base-300 rounded-lg p-4 text-center cursor-pointer hover:border-accent1 hover:bg-base-100 transition file-input-wrapper"
                                    style="border-color: rgba(255,255,255,0.2); background-color: rgba(255,255,255,0.03);"
                                    onmouseenter="this.style.borderColor='var(--color-accent1)'; this.style.backgroundColor='rgba(255,255,255,0.1)'"
                                    onmouseleave="this.style.borderColor='rgba(255,255,255,0.2)'; this.style.backgroundColor='rgba(255,255,255,0.03)'"
                                    ondrop="handleDrop(event, 'beneficiary_file', 'beneficiary_selectedName', 'beneficiary_fileName', 'beneficiary_dropText')"
                                    ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)"
                                    id="beneficiary_dropZone">
                                    <input type="file" name="document_file[]" id="beneficiary_file"
                                        class="hidden" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf" multiple>
                                    <div id="beneficiary_dropText" class="pointer-events-none">
                                        <svg class="w-8 h-8 mx-auto mb-2" style="color: rgba(255,255,255,0.4);"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z">
                                            </path>
                                        </svg>
                                        <p class="text-sm font-medium" style="color: rgba(255,255,255,0.6);">Drop file
                                            here or click to select</p>
                                        <p class="text-xs mt-1" style="color: rgba(255,255,255,0.4);">JPG, PNG, GIF,
                                            WebP, or PDF</p>
                                    </div>
                                    <div id="beneficiary_fileName" class="hidden">
                                        <div id="beneficiary_fileList" class="space-y-1"></div>
                                        <div class="flex gap-2 mt-2">
                                            <button type="button" id="beneficiary_addFiles"
                                                class="text-base text-accent1 hover:text-yellow-300 hidden">+
                                                Add</button>
                                            <button type="button" id="beneficiary_clearFiles"
                                                class="text-base text-red-400 hover:text-red-300 hidden">-
                                                Clear</button>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <button type="button" id="extractDataBtn"
                                class="mt-3 px-4 py-2 text-sm font-semibold rounded-lg hidden transition-all"
                                style="background-color: var(--color-accent1); color: var(--color-primary1);">
                                <span id="extractBtnText">Extract Data from Document</span>
                                <span id="extractBtnSpinner" class="hidden">
                                    <svg class="animate-spin w-4 h-4 inline" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                </span>
                            </button>
                            <div id="extractStatus" class="mt-2 text-sm hidden"></div>

                            <x-forms.errors name="document_file" />
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="rounded-xl overflow-hidden mt-6" style="border: 1px solid rgba(255,255,255,0.1);">
                        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                            style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                    style="background-color: rgba(255,204,51,0.15);">
                                    <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">
                                        Personal Information</h3>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block mb-2 text-sm font-semibold"
                                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">First
                                        Name <span class="text-xs font-normal"
                                            style="color: var(--color-danger);">*</span></label>
                                    <input type="text" name="first_name"
                                        class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                        value="{{ old('first_name') }}" placeholder="Enter first name" required>
                                    <x-forms.errors name="first_name" />
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-semibold"
                                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Middle
                                        Name</label>
                                    <input type="text" name="middle_name"
                                        class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                        value="{{ old('middle_name') }}" placeholder="Enter middle name">
                                    <x-forms.errors name="middle_name" />
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-semibold"
                                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Last
                                        Name <span class="text-xs font-normal"
                                            style="color: var(--color-danger);">*</span></label>
                                    <input type="text" name="last_name"
                                        class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                        value="{{ old('last_name') }}" placeholder="Enter last name" required>
                                    <x-forms.errors name="last_name" />
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-semibold"
                                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Extension
                                        (Jr, Sr)</label>
                                    <input type="text" name="name_extension"
                                        class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                        value="{{ old('name_extension') }}" placeholder="e.g., Jr, Sr">
                                    <x-forms.errors name="name_extension" />
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-semibold"
                                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Birth
                                        Date <span class="text-xs font-normal"
                                            style="color: var(--color-danger);">*</span></label>
                                    <input type="date" name="birth_date"
                                        class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                        value="{{ old('birth_date') }}" required>
                                    <x-forms.errors name="birth_date" />
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-semibold"
                                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Sex <span
                                            class="text-xs font-normal"
                                            style="color: var(--color-danger);">*</span></label>
                                    <div class="flex gap-4 pt-2">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="sex" value="male"
                                                class="w-4 h-4 accent-accent1" @checked(old('sex') === 'male') required>
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
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="rounded-xl overflow-hidden mt-6" style="border: 1px solid rgba(255,255,255,0.1);">
                        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                            style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                    style="background-color: rgba(255,204,51,0.15);">
                                    <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">
                                        Contact Information</h3>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block mb-2 text-sm font-semibold"
                                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Email</label>
                                        <input type="email" name="email"
                                            class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                            value="{{ old('email') }}" placeholder="example@mail.com">
                                        <x-forms.errors name="email" />
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-semibold"
                                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Contact
                                            Number
                                            <span class="text-xs font-normal"
                                                style="color: var(--color-danger);">*</span></label>
                                        <x-forms.phone-input name="contact_number"
                                            value="{{ old('contact_number') }}" :required="true" />
                                    </div>
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-semibold"
                                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Address
                                        Line <span class="text-xs font-normal"
                                            style="color: var(--color-danger);">*</span></label>
                                    <input type="text" name="address_line"
                                        class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                        value="{{ old('address_line') }}"
                                        placeholder="Street address, Purok, Barangay" required>
                                    <x-forms.errors name="address_line" />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block mb-2 text-sm font-semibold"
                                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Country
                                            <span class="text-xs font-normal"
                                                style="color: var(--color-danger);">*</span></label>
                                        <input type="text" name="country"
                                            class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                            value="{{ old('country', 'Philippines') }}" placeholder="e.g., Philippines" required>
                                        <x-forms.errors name="country" />
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-semibold"
                                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">State
                                            / Province <span class="text-xs font-normal"
                                                style="color: var(--color-danger);">*</span></label>
                                        <input type="text" name="province"
                                            class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                            value="{{ old('province', $isEducation ? 'Metro Manila' : '') }}" placeholder="e.g., Metro Manila" required>
                                        <x-forms.errors name="province" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block mb-2 text-sm font-semibold"
                                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">City
                                            <span class="text-xs font-normal"
                                                style="color: var(--color-danger);">*</span></label>
                                        <input type="text" name="city"
                                            class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                            value="{{ old('city', $isEducation ? 'Quezon City' : '') }}" placeholder="e.g., Quezon City" required>
                                        <x-forms.errors name="city" />
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-semibold"
                                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Zip
                                            <span class="text-xs font-normal"
                                                style="color: var(--color-danger);">*</span></label>
                                        <input type="text" name="zip"
                                            class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                            value="{{ old('zip', $isEducation ? '119' : '') }}" placeholder="e.g., 1100" required>
                                        <x-forms.errors name="zip" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Other Information -->
                    <div class="rounded-xl overflow-hidden mt-6" style="border: 1px solid rgba(255,255,255,0.1);">
                        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                            style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                    style="background-color: rgba(255,204,51,0.15);">
                                    <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">
                                        Other Information</h3>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex gap-6">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="form_given" value="1"
                                        class="w-4 h-4 rounded border-gray-300 accent-accent1"
                                        @checked(old('form_given')) checked>
                                    <span class="text-sm" style="color: white;">Form Given</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="with_disability" value="1"
                                        class="w-4 h-4 rounded border-gray-300 accent-accent1"
                                        @checked(old('with_disability'))>
                                    <span class="text-sm" style="color: white;">With Disability</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-end gap-3 pt-6 mt-6 border-t"
                        style="border-color: rgba(255,255,255,0.1);">
                        <button type="button" onclick="switchTab('guardian')"
                            class="px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] inline-flex items-center gap-2"
                            style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                            {{-- Next --}}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Tab Content: Guardian -->
                <div id="guardian-tab" class="tab-content" style="display: none;">
                    <div class="rounded-xl overflow-hidden">
                        <div>
                            @include('beneficiaries.partials.guardian-fields', [
                                'guardianEntries' => old('guardians', [[]]),
                                'guardianNextIndex' => count(old('guardians', [[]])),
                                'isEducation' => $isEducation,
                            ])
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-between gap-3 pt-6 mt-6 border-t"
                        style="border-color: rgba(255,255,255,0.1);">
                        <button type="button" onclick="switchTab('beneficiary')"
                            class="px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] inline-flex items-center gap-2"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.06); color: white; border: 1px solid rgba(255,255,255,0.1);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                            </svg>
                            {{-- Back --}}
                        </button>
                        <button type="button" onclick="switchTab('{{ $isEducation ? 'intake' : 'status' }}')"
                            class="px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] inline-flex items-center gap-2"
                            style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                            {{-- Next --}}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Tab Content: Intake Sheet -->
                @if ($isEducation)
                    <div id="intake-tab" class="tab-content" style="display: none;">
                        <div class="rounded-xl overflow-hidden">
                            <div>
                                @include('beneficiaries.partials.intake-fields', [
                                    'intake' => old('intake', []),
                                ])
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="flex justify-between gap-3 pt-6 mt-6 border-t"
                            style="border-color: rgba(255,255,255,0.1);">
                            <button type="button" onclick="switchTab('guardian')"
                                class="px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] inline-flex items-center gap-2"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.06); color: white; border: 1px solid rgba(255,255,255,0.1);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                                </svg>
                                {{-- Back --}}
                            </button>
                            <button type="button" onclick="switchTab('status')"
                                class="px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] inline-flex items-center gap-2"
                                style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                                {{-- Next --}}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Tab Content: Program Status -->
                <div id="status-tab" class="tab-content" style="display: none;">
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
                                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">
                                        Program Status</h3>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block mb-2 text-sm font-semibold"
                                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Beneficiary
                                        Status <span class="text-xs font-normal"
                                            style="color: var(--color-danger);">*</span></label>
                                    <div class="relative">
                                        <select name="status"
                                            class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none"
                                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;"
                                            required>
                                            @foreach ($status_types as $statusId => $statusLabel)
                                                <option value="{{ $statusId }}"
                                                    style="color: var(--color-primary1);" @selected((string) old('status') === (string) $statusId)>
                                                    {{ $statusLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <x-forms.errors name="status" />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block mb-2 text-sm font-semibold"
                                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Start
                                            Date <span class="text-xs font-normal"
                                                style="color: var(--color-danger);">*</span></label>
                                        <input type="date" name="start_date"
                                            class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                            value="{{ old('start_date') }}" required>
                                        <x-forms.errors name="start_date" />
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-semibold"
                                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">End
                                            Date</label>
                                        <input type="date" name="end_date"
                                            class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);"
                                            value="{{ old('end_date') }}">
                                        <x-forms.errors name="end_date" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-between gap-3 pt-6 mt-6 border-t"
                        style="border-color: rgba(255,255,255,0.1);">
                        <button type="button" onclick="switchTab('{{ $isEducation ? 'intake' : 'guardian' }}')"
                            class="max-xs:text-sm px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] inline-flex items-center gap-2"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.06); color: white; border: 1px solid rgba(255,255,255,0.1);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                            </svg>
                            {{-- Back --}}
                        </button>
                        <div class="flex gap-3">
                            <a href="/beneficiaries"
                                class="max-xs:text-sm px-6 py-2.5 font-semibold rounded-lg xs:rounded-sm transition-all duration-300 hover:scale-[1.02] bg-transparent text-red-600 border border-red-600 hover:bg-red-50 hover:text-red-700 inline-flex items-center gap-2"
                                style="font-family: var(--font-body1);">
                                X
                            </a>
                            <button type="submit"
                                class="max-xs:text-sm px-6 py-2.5 font-semibold rounded-lg xs:rounded-sm transition-all duration-300 hover:scale-[1.02] inline-flex items-center gap-2"
                                style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                                Done
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function switchTab(tabName) {
            const tabs = ['beneficiary', 'guardian', 'intake', 'status'];
            const tabBtns = ['beneficiaryTabBtn', 'guardianTabBtn', 'intakeTabBtn', 'statusTabBtn'];

            tabs.forEach(tab => {
                const element = document.getElementById(tab + '-tab');
                if (element) {
                    element.style.display = 'none';
                }
            });

            tabBtns.forEach(btn => {
                const button = document.getElementById(btn);
                if (button) {
                    button.style.color = 'rgba(255,255,255,0.6)';
                    button.style.borderBottom = '2px solid transparent';
                }
            });

            const activeTab = document.getElementById(tabName + '-tab');
            if (activeTab) {
                activeTab.style.display = 'block';
            }

            const activeBtn = document.getElementById(tabName + 'TabBtn');
            if (activeBtn) {
                activeBtn.style.color = 'var(--color-accent1)';
                activeBtn.style.borderBottom = '2px solid var(--color-accent1)';
            }
        }

        // Set active tab based on errors
        @php
            $activeTab = 'beneficiary';
            if ($hasGuardianErrors) {
                $activeTab = 'guardian';
            } elseif ($hasIntakeErrors) {
                $activeTab = 'intake';
            } elseif ($hasStatusErrors) {
                $activeTab = 'status';
            }
        @endphp

        document.addEventListener('DOMContentLoaded', function() {
            switchTab('{{ $activeTab }}');
        });
    </script>

    @include('beneficiaries.partials.guardian-script')

    @vite('resources/js/beneficiary-auto-fill.js')

    <script>
        function handleDragOver(e) {
            e.preventDefault();
            e.currentTarget.classList.add('border-accent1', 'bg-base-100');
        }

        function handleDragLeave(e) {
            e.preventDefault();
            e.currentTarget.classList.remove('border-accent1', 'bg-base-100');
        }

        window.beneficiaryFiles = [];

        function renderFileList() {
            const list = document.getElementById('beneficiary_fileList');
            const clearBtn = document.getElementById('beneficiary_clearFiles');
            const addBtn = document.getElementById('beneficiary_addFiles');
            list.innerHTML = '';
            if (window.beneficiaryFiles.length === 0) {
                document.getElementById('beneficiary_fileName').classList.add('hidden');
                document.getElementById('beneficiary_dropText').classList.remove('hidden');
                document.getElementById('extractDataBtn').classList.add('hidden');
                if (clearBtn) clearBtn.classList.add('hidden');
                if (addBtn) addBtn.classList.add('hidden');
                return;
            }
            document.getElementById('beneficiary_fileName').classList.remove('hidden');
            document.getElementById('beneficiary_dropText').classList.add('hidden');
            document.getElementById('extractDataBtn').classList.remove('hidden');
            if (clearBtn) clearBtn.classList.remove('hidden');
            if (addBtn) addBtn.classList.remove('hidden');

            window.beneficiaryFiles.forEach((f, i) => {
                const size = (f.size / (1024 * 1024)).toFixed(2);
                const div = document.createElement('div');
                div.className = 'flex items-center justify-between gap-2 py-1 px-2 rounded text-sm';
                div.style.cssText = 'background-color: rgba(255,255,255,0.05);';
                div.innerHTML = `
                    <span style="color: var(--color-success);">\u2713 ${f.name} (${size} MB)</span>
                    <button type="button" data-index="${i}" class="remove-file-btn text-red-400 hover:text-red-300 font-bold leading-none text-2xl">\u00d7</button>
                `;
                list.appendChild(div);
            });

            list.querySelectorAll('.remove-file-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const idx = parseInt(this.dataset.index);
                    window.beneficiaryFiles.splice(idx, 1);
                    renderFileList();
                });
            });

            if (clearBtn) {
                clearBtn.onclick = function() {
                    window.beneficiaryFiles = [];
                    renderFileList();
                };
            }

            if (addBtn) {
                addBtn.onclick = function() {
                    document.getElementById('beneficiary_file').click();
                };
            }
        }

        function handleDrop(e, inputId, selectedNameId, fileNameId, dropTextId) {
            e.preventDefault();
            const dropZone = e.currentTarget;
            dropZone.classList.remove('border-accent1', 'bg-base-100');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                addFiles(files);
            }
        }

        function addFiles(files) {
            for (const f of files) {
                if ((f.size / (1024 * 1024)) > 5) {
                    alert(`File "${f.name}" exceeds 5 MB limit`);
                    continue;
                }
                window.beneficiaryFiles.push(f);
            }
            if (window.beneficiaryFiles.length > 0) {
                renderFileList();
                document.getElementById('extractDataBtn').classList.remove('hidden');
            }
        }

        document.getElementById('beneficiary_file')?.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                addFiles(this.files);
            }
        });

        document.querySelector('form.requires-program-checkbox').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.delete('document_file[]');
            formData.delete('document_file');
            window.beneficiaryFiles.forEach(file => {
                formData.append('document_file[]', file);
            });

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                    },
                    body: formData,
                });

                if (response.ok) {
                    const data = await response.json();
                    window.location.href = data.redirect;
                } else if (response.status === 422) {
                    const result = await response.json();
                    document.querySelectorAll('.error.text-red-500').forEach(el => el.remove());
                    Object.entries(result.errors || {}).forEach(([field, messages]) => {
                        const input = document.querySelector(`[name="${field}"]`);
                        if (input) {
                            const errorEl = document.createElement('p');
                            errorEl.className = 'error text-red-500';
                            errorEl.textContent = messages[0];
                            input.parentNode.appendChild(errorEl);
                        }
                    });
                    const firstField = Object.keys(result.errors || {})[0];
                    if (firstField) {
                        let tab = 'beneficiary';
                        if (firstField.startsWith('guardians.')) {
                            tab = 'guardian';
                        } else if (firstField.startsWith('intake.')) {
                            tab = 'intake';
                        } else if (['status', 'start_date', 'end_date'].includes(firstField) || firstField.startsWith('status.')) {
                            tab = 'status';
                        }
                        switchTab(tab);
                    }
                    const firstError = document.querySelector('.error.text-red-500');
                    if (firstError) {
                        requestAnimationFrame(() => firstError.scrollIntoView({ behavior: 'smooth', block: 'center' }));
                    }
                } else {
                    const data = await response.json().catch(() => ({ message: 'An error occurred.' }));
                    alert(data.message || 'An error occurred. Please try again.');
                }
            } catch (error) {
                alert('A network error occurred. Please check your connection.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                submitBtn.textContent = originalText;
            }
        });
    </script>
</x-dashboardlayout>
