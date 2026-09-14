<x-layout title="Log In">
    <div class="min-h-screen max-h-screen w-full relative flex items-center justify-center py-12 px-4 md:px-6">
        <!-- Left Side - Image -->
        <div class="relative z-10 w-full max-w-5xl -mt-20 md:-mt-24">
            <!-- Success Message -->
            @if (session('status'))
                <div class="mb-6 p-4 rounded-xl flex items-center gap-3"
                    style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3);">
                    <svg class="w-5 h-5 shrink-0" style="color: #10b981;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm"
                        style="color: #10b981; font-family: var(--font-body1);">{{ session('status') }}</span>
                </div>
            @endif
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
                        <h2 class="text-3xl font-bold mb-3 text-white" style="font-family: var(--font-header1);">Welcome
                            Back!</h2>
                        <div class="w-12 h-0.5 mx-auto mb-5 rounded-full"
                            style="background-color: var(--color-accent1);"></div>
                        <p class="text-white/80 text-sm max-w-xs" style="font-family: var(--font-body1);">Leveling the
                            playing field.</p>
                    </div>
                </div>

                <!-- Right Side - Login Form -->
                <div class="w-full md:w-3/5 flex items-center justify-center p-8 md:p-10"
                    style="background-color: rgba(255, 255, 255, 0.95);">
                    <div class="w-full max-w-md">
                        <!-- Mobile Logo -->
                        <div class="text-center mb-8 md:hidden">
                            <img src="{{ asset('images/FAIRALL_LOGO.png') }}" alt="FAIRALL Logo"
                                class="h-12 mx-auto mb-3">
                            <h2 class="text-2xl font-bold"
                                style="font-family: var(--font-header1); color: var(--color-primary1);">Welcome Back!
                            </h2>
                            <div class="w-12 h-0.5 mx-auto mt-2 rounded-full"
                                style="background-color: var(--color-accent1);"></div>
                        </div>

                        <div class="text-center mb-6 hidden md:block">
                            <h2 class="text-2xl font-bold"
                                style="font-family: var(--font-header1); color: var(--color-primary1);">Log In</h2>
                            <div class="w-12 h-0.5 mx-auto mt-2 rounded-full"
                                style="background-color: var(--color-accent1);"></div>
                            <p class="text-gray-500 mt-2 text-sm" style="font-family: var(--font-body1);">Access your
                                account</p>
                        </div>

                        <form method="POST" action="/login" class="space-y-5">
                            @csrf

                            <!-- Email or Login ID Field -->
                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: var(--color-primary1);">Email or Login
                                    ID <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                                <input type="text" name="email" id="email"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-yellow-400/50 transition-all"
                                    style="font-family: var(--font-body1); background-color: #f9fafb; color: var(--color-primary1);"
                                    value="{{ old('email') }}" required>
                            </div>

                            <!-- Password Field with Eye Icon -->
                            <div>
                                <label class="block mb-2 text-sm font-semibold"
                                    style="font-family: var(--font-body1); color: var(--color-primary1);">Password <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                                <div class="relative">
                                    <input type="password" name="password" id="password"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-yellow-400/50 transition-all pr-12"
                                        style="font-family: var(--font-body1); background-color: #f9fafb; color: var(--color-primary1);"
                                        placeholder="••••••••" required>
                                </div>
                            </div>

                            <!-- Error Messages -->
                            <x-forms.errors name="password" />

                            <!-- Forgot Password & Remember Me -->
                            <div class="flex items-center justify-between">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="remember" id="remember" value="1"
                                        class="w-4 h-4 rounded border-gray-300 focus:ring-yellow-400">
                                    <span class="text-sm text-gray-600" style="font-family: var(--font-body1);">Remember
                                        me</span>
                                </label>
                                <a href="/forgot-password" class="text-sm transition-all duration-200 hover:underline"
                                    style="font-family: var(--font-body1); color: var(--color-primary1); font-weight: 500;">Forgot
                                    password?</a>
                            </div>

                            <!-- Login Button -->
                            <button type="submit"
                                class="w-full py-3 font-semibold rounded-full transition-all duration-300 hover:scale-[1.02]"
                                style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                                Log In
                            </button>

                            <!-- Sign Up Link -->
                            <div class="text-center pt-4">
                                <p class="text-sm text-gray-500" style="font-family: var(--font-body1);">
                                    No account yet?
                                    <a href="/register" class="font-semibold hover:underline"
                                        style="color: var(--color-primary1);">Sign up for a donor account</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
