@push('head-scripts')
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <link rel="preconnect" href="https://challenges.cloudflare.com" />
@endpush

<x-layout title="Sign Up as Individual Donor">
    <div class="min-h-screen w-full relative flex items-start justify-center pt-33 pb-12 px-4 md:px-6">
        <!-- Left Side - Image -->
        <div class="relative z-10 w-full max-w-6xl -mt-20 md:-mt-24">
            <div class="flex flex-col md:flex-row rounded-2xl overflow-hidden shadow-2xl">
                <!-- Left Side - Hero Image inside Container -->
                <div class="hidden md:block md:w-2/5 relative overflow-hidden">
                    <img src="{{ asset('images/HERO.jpg') }}" alt="Welcome"
                        class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0"
                        style="background: linear-gradient(135deg, rgba(28,33,67,0.85) 0%, rgba(22,33,114,0.75) 100%);">
                    </div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-8">
                        <img src="{{ asset('images/FAIRALL_LOGO.png') }}" alt="FAIRALL Logo" class="h-16 mx-auto mb-4">
                        <h2 class="text-3xl font-bold mb-3 text-white" style="font-family: var(--font-header1);">Join
                            Our Community</h2>
                        <div class="w-12 h-0.5 mx-auto mb-5 rounded-full"
                            style="background-color: var(--color-accent1);"></div>
                        <p class="text-white/80 text-sm max-w-xs" style="font-family: var(--font-body1);">Leveling the
                            playing field.</p>
                    </div>
                </div>

                <!-- Right Side - Registration Form -->
                <div class="w-full md:w-3/5 flex items-center justify-center p-6 md:p-8"
                    style="background-color: rgba(255, 255, 255, 0.95);">
                    <div class="w-full">
                        <!-- Mobile Logo -->
                        <div class="text-center mb-6 md:hidden">
                            <img src="{{ asset('images/FAIRALL_LOGO.png') }}" alt="FAIRALL Logo"
                                class="h-12 mx-auto mb-3">
                            <h2 class="text-2xl font-bold"
                                style="font-family: var(--font-header1); color: var(--color-primary1);">Individual Donor
                            </h2>
                            <div class="w-12 h-0.5 mx-auto mt-2 rounded-full"
                                style="background-color: var(--color-accent1);"></div>
                        </div>

                        <div class="text-center mb-6 hidden md:block">
                            <h2 class="text-2xl font-bold"
                                style="font-family: var(--font-header1); color: var(--color-primary1);">Individual Donor
                                Registration</h2>
                            <div class="w-12 h-0.5 mx-auto mt-2 rounded-full"
                                style="background-color: var(--color-accent1);"></div>
                            <p class="text-gray-500 mt-2 text-sm" style="font-family: var(--font-body1);">Create your
                                account to start donating</p>
                        </div>

                        <form method="POST" action="/register" class="space-y-4">
                            @csrf
                            <input type="hidden" name="donor_type" value="individual">

                            <!-- Name Rows -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-2 text-sm font-semibold"
                                        style="font-family: var(--font-body1); color: var(--color-primary1);">First Name
                                        <span class="text-xs font-normal"
                                            style="color: var(--color-danger);">*</span></label>
                                    <input type="text" name="first_name"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-yellow-400/50 transition-all"
                                        style="font-family: var(--font-body1); background-color: #f9fafb; color: var(--color-primary1);"
                                        placeholder="John" value="{{ old('first_name') }}" required>
                                    <x-forms.errors name="first_name" />
                                </div>

                                <!-- Middle Name -->
                                <div>
                                    <label class="block mb-2 text-sm font-semibold"
                                        style="font-family: var(--font-body1); color: var(--color-primary1);">Middle
                                        Name
                                        <span class="text-gray-400 font-normal">(Optional)</span></label>
                                    <input type="text" name="middle_name"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-yellow-400/50 transition-all"
                                        style="font-family: var(--font-body1); background-color: #f9fafb; color: var(--color-primary1);"
                                        placeholder="Michael" value="{{ old('middle_name') }}">
                                    <x-forms.errors name="middle_name" />
                                </div>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: var(--color-primary1);">Last Name
                                    <span class="text-xs font-normal"
                                        style="color: var(--color-danger);">*</span></label>
                                <input type="text" name="last_name"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-yellow-400/50 transition-all"
                                    style="font-family: var(--font-body1); background-color: #f9fafb; color: var(--color-primary1);"
                                    placeholder="Doe" value="{{ old('last_name') }}" required>
                                <x-forms.errors name="last_name" />
                            </div>

                            <!-- Contact Number & Email Row -->
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block mb-2 text-sm font-semibold"
                                        style="font-family: var(--font-body1); color: var(--color-primary1);">Contact
                                        Number
                                        <span class="text-xs font-normal"
                                            style="color: var(--color-danger);">*</span></label>
                                    <x-forms.phone-input name="contact_number" value="{{ old('contact_number') }}"
                                        :required="true" />
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-semibold"
                                        style="font-family: var(--font-body1); color: var(--color-primary1);">Email
                                        Address <span class="text-xs font-normal"
                                            style="color: var(--color-danger);">*</span></label>
                                    <input type="email" name="email"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-yellow-400/50 transition-all"
                                        style="font-family: var(--font-body1); background-color: #f9fafb; color: var(--color-primary1);"
                                        placeholder="you@example.com" value="{{ old('email') }}" required>
                                    <x-forms.errors name="email" />
                                </div>
                            </div>

                            <!-- Password & Confirm Password Row -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-2 text-sm font-semibold"
                                        style="font-family: var(--font-body1); color: var(--color-primary1);">Password
                                        <span class="text-xs font-normal"
                                            style="color: var(--color-danger);">*</span></label>
                                    <div class="relative">
                                        <input type="password" name="password" id="password"
                                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-yellow-400/50 transition-all pr-12"
                                            style="font-family: var(--font-body1); background-color: #f9fafb; color: var(--color-primary1);"
                                            placeholder="••••••••" required>
                                    </div>
                                    <x-forms.errors name="password" />
                                    <ul id="password-requirements" class="mt-1 space-y-0.5"></ul>
                                    <script>document.addEventListener('DOMContentLoaded',function(){initPasswordRequirements('password','password-requirements',{failedClass:'text-primary1'})});</script>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-semibold"
                                        style="font-family: var(--font-body1); color: var(--color-primary1);">Confirm
                                        Password <span class="text-xs font-normal"
                                            style="color: var(--color-danger);">*</span></label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-yellow-400/50 transition-all"
                                        style="font-family: var(--font-body1); background-color: #f9fafb; color: var(--color-primary1);"
                                        placeholder="••••••••" required>
                                    <x-forms.errors name="password_confirmation" />
                                    <ul id="confirm-password-requirements" class="mt-2 space-y-0.5"></ul>
                                    <script>document.addEventListener('DOMContentLoaded',function(){initPasswordMatch('password','password_confirmation','confirm-password-requirements',{failedClass:'text-primary1'})});</script>
                                </div>
                            </div>

                            <!-- Cloudflare Turnstile -->
                            <div class="flex justify-center">
                                <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-theme="light"></div>
                            </div>
                            <x-forms.errors name="cf-turnstile-response" />

                            <!-- Sign Up Button -->
                            <button type="submit"
                                class="w-full py-3 font-semibold rounded-full transition-all duration-300 hover:scale-[1.02] mt-6"
                                style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                                Create Account
                            </button>

                            <!-- Back to Type Selection -->
                            <div class="text-center pt-4">
                                <p class="text-sm text-gray-500" style="font-family: var(--font-body1);">
                                    Want to register as an organization?
                                    <a href="/register" class="font-semibold hover:underline"
                                        style="color: var(--color-primary1);">Go back</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layout>
