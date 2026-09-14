<x-dashboardlayout name="{{ $name }}" title="Edit Donor Account">
    <div class="mb-8">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight" style="font-family: var(--font-header1); color: var(--color-white);">
                    Edit Donor Account
                </h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                    Update the linked user account for this donor
                </p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
        </div>

        <!-- Donor Profile Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl mb-6" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white text-sm sm:text-base" style="font-family: var(--font-header1);">Donor Profile</h3>
                        <p class="text-xs" style="color: rgba(255,255,255,0.5);">Linked donor information</p>
                    </div>
                </div>
            </div>
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Name</p>
                        <p class="text-sm font-medium text-white mt-1" style="font-family: var(--font-body1);">{{ $donor?->display_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Contact</p>
                        <p class="text-sm text-white/70 mt-1" style="font-family: var(--font-body1);">{{ $donor?->formatted_contact ?? 'N/A' }}</p>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-1">
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Donor Type</p>
                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full mt-1" style="background-color: rgba(255,204,51,0.15); color: var(--color-accent1); font-family: var(--font-body1);">
                            {{ ucfirst($donor?->donor_type ?? 'N/A') }}
                        </span>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-1">
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Email</p>
                        <p class="text-sm text-white/70 mt-1" style="font-family: var(--font-body1);">{{ $user->email ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Information Form -->
        <form method="POST" action="/users/{{ $user->id }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white text-sm sm:text-base" style="font-family: var(--font-header1);">Account Information</h3>
                            <p class="text-xs" style="color: rgba(255,255,255,0.5);">Update the user's login credentials</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Login ID <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="text" name="login_id" 
                                class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                                value="{{ old('login_id', $user->login_id) }}" placeholder="Enter login ID" required>
                            <x-forms.errors name="login_id" />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Email <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="email" name="email" 
                                class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                                value="{{ old('email', $user->email) }}" placeholder="donor@example.com" required>
                            <x-forms.errors name="email" />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">New Password <span class="text-white/40 text-xs font-normal">(optional)</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5" style="color: var(--color-primary1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
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
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Confirm Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5" style="color: var(--color-primary1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
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

                    <!-- Active Status -->
                    <div class="mt-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" 
                                class="w-4 h-4 rounded border focus:ring-2 focus:ring-accent1 transition-all duration-200 cursor-pointer"
                                style="accent-color: var(--color-accent1); border-color: rgba(255,255,255,0.2); background-color: rgba(255,255,255,0.05);"
                                {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <span class="text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Active Account</span>
                        </label>
                        <p class="text-xs text-white/40 mt-1" style="font-family: var(--font-body1);">Inactive users cannot log in to the system</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4">
                <a href="/users?tab=donors" 
                    class="px-6 py-3 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] text-center inline-flex items-center justify-center gap-2 group"
                    style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                    onmouseover="this.style.backgroundColor='rgba(248,113,113,0.1)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)'"
                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-danger)'; this.style.borderColor='var(--color-danger)'">
                    <svg class="w-4 h-4 transition-all duration-300 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancel
                </a>
                <button type="submit" 
                    class="px-6 py-3 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center justify-center gap-2 group"
                    style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                    onmouseover="this.style.filter='brightness(0.95)'"
                    onmouseout="this.style.filter='brightness(1)'">
                    <svg class="w-4 h-4 transition-all duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"></path>
                    </svg>
                    Save Account
                </button>
            </div>
        </form>
    </div>
</x-dashboardlayout>