<x-dashboardlayout name="{{ $name }}" title="Create Donor">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight"
                    style="font-family: var(--font-header1); color: var(--color-white);">Create Donor</h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">Create the
                    donor account and send a verification invite with a temporary password.</p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
        </div>


        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl flex items-start gap-3"
                style="background-color: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.3);">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--color-danger);" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                </svg>
                <div>
                    <span class="text-sm font-semibold" style="color: var(--color-danger);">Please fix the following
                        errors:</span>
                    <ul class="mt-1 text-sm space-y-1" style="color: rgba(255,255,255,0.7);">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif


        <form method="POST" action="{{ route('donors.store') }}" class="space-y-6">
            @csrf

            <div class="rounded-2xl overflow-hidden"
                style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-6 py-4 border-b"
                    style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z">
                                </path>
                            </svg>
                        </div>
                        <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Donor Information
                        </h2>
                    </div>
                </div>


                <div class="p-6 space-y-5">
                    <!-- Row 1: Donor Type -->
                    <div>
                        <label class="block mb-2 text-xs font-semibold uppercase tracking-wider"
                            style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Donor Type</label>
                        <div class="relative">
                            <select name="donor_type" id="donor_type"
                                class="w-full rounded-xl px-4 py-2.5 appearance-none transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1 cursor-pointer"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">
                                <option value="individual"
                                    style="background-color: var(--color-primary1); color: white;">Individual</option>
                                <option value="organization"
                                    style="background-color: var(--color-primary1); color: white;">Organization</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                </svg>
                            </div>
                        </div>
                        <x-forms.errors name="donor_type" />
                    </div>


                    <!-- Row 2: Individual Fields (First, Middle, Last Name) -->
                    <div id="individual-fields" class="grid gap-5 md:grid-cols-3">
                        <div>
                            <label class="block mb-2 text-xs font-semibold uppercase tracking-wider"
                                style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">First Name</label>
                            <input type="text" name="first_name"
                                class="w-full rounded-xl px-4 py-2.5 text-primary1 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);"
                                value="{{ old('first_name') }}" placeholder="Enter first name">
                            <x-forms.errors name="first_name" />
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-semibold uppercase tracking-wider"
                                style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Middle
                                Name</label>
                            <input type="text" name="middle_name"
                                class="w-full rounded-xl px-4 py-2.5 text-primary1 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);"
                                value="{{ old('middle_name') }}" placeholder="Enter middle name">
                            <x-forms.errors name="middle_name" />
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-semibold uppercase tracking-wider"
                                style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Last Name</label>
                            <input type="text" name="last_name"
                                class="w-full rounded-xl px-4 py-2.5 text-primary1 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);"
                                value="{{ old('last_name') }}" placeholder="Enter last name">
                            <x-forms.errors name="last_name" />
                        </div>
                    </div>


                    <!-- Row 3: Email and Contact Number -->
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="block mb-2 text-xs font-semibold uppercase tracking-wider"
                                style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Email <span
                                    class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="email" name="email"
                                class="w-full rounded-xl px-4 py-2.5 text-primary1 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);"
                                value="{{ old('email') }}" placeholder="Enter email address" required>
                            <x-forms.errors name="email" />
                        </div>

                        <div>
                            <label class="block mb-2 text-xs font-semibold uppercase tracking-wider"
                                style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Contact Number <span
                                    class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <x-forms.phone-input name="contact_number" value="{{ old('contact_number') }}"
                                :required="true" />
                        </div>
                    </div>


                    <!-- Row 4: Organization Field -->
                    <div id="organization-fields" class="hidden">
                        <label class="block mb-2 text-xs font-semibold uppercase tracking-wider"
                            style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Organization
                            Name</label>
                        <input type="text" name="organization_name"
                            class="w-full rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1 text-primary1"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);"
                            value="{{ old('organization_name') }}" placeholder="Enter organization name">
                        <x-forms.errors name="organization_name" />
                    </div>
                </div>
            </div>


            <!-- Form Actions -->
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4">
                <a href="{{ route('donors.index') }}"
                    class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] text-center inline-flex items-center justify-center gap-2 group"
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
                    class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center justify-center gap-2 group"
                    style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                    onmouseover="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(0.95)'"
                    onmouseout="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(1)'">
                    <svg class="w-4 h-4 transition-all duration-300 group-hover:translate-x-1" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.5v15m7.5-7.5h-15"></path>
                    </svg>
                    Create Donor
                </button>
            </div>
        </form>
    </div>


    <script>
        const donorType = document.getElementById('donor_type');
        const individualFields = document.getElementById('individual-fields');
        const organizationFields = document.getElementById('organization-fields');


        function syncDonorFields() {
            if (donorType.value === 'organization') {
                individualFields.classList.add('hidden');
                organizationFields.classList.remove('hidden');
            } else {
                individualFields.classList.remove('hidden');
                organizationFields.classList.add('hidden');
            }
        }


        donorType.addEventListener('change', syncDonorFields);
        syncDonorFields();
    </script>
</x-dashboardlayout>
