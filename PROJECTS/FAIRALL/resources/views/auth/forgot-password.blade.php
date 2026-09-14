<x-layout title="Forgot Password">
    <div class="h-screen w-full relative flex items-center justify-center px-4 md:px-6">
        <!-- Left Side - Image -->
        <div class="relative z-10 w-full max-w-5xl -mt-20 md:-mt-24">
            <div class="flex flex-col md:flex-row rounded-2xl overflow-hidden shadow-2xl">
                <!-- Left Side - Hero Image -->
                <div class="hidden md:block md:w-2/5 relative overflow-hidden">
                    <img src="{{ asset('images/HERO.jpg') }}" alt="Welcome" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(28,33,67,0.85) 0%, rgba(22,33,114,0.75) 100%);"></div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-8">
                        <img src="{{ asset('images/FAIRALL_LOGO.png') }}" alt="FAIRALL Logo" class="h-16 mx-auto mb-4">
                        <h2 class="text-3xl font-bold mb-3 text-white" style="font-family: var(--font-header1);">Reset Password</h2>
                        <div class="w-12 h-0.5 mx-auto mb-5 rounded-full" style="background-color: var(--color-accent1);"></div>
                        <p class="text-white/80 text-sm max-w-xs" style="font-family: var(--font-body1);">We'll help you get back into your account</p>
                    </div>
                </div>

                <!-- Right Side - Forgot Password Form -->
                <div class="w-full md:w-3/5 flex items-center justify-center p-8 md:p-10" style="background-color: rgba(255, 255, 255, 0.95);">
                    <div class="w-full max-w-md">
                        <!-- Mobile Logo -->
                        <div class="text-center mb-8 md:hidden">
                            <img src="{{ asset('images/FAIRALL_LOGO.png') }}" alt="FAIRALL Logo" class="h-12 mx-auto mb-3">
                            <h2 class="text-2xl font-bold" style="font-family: var(--font-header1); color: var(--color-primary1);">Reset Password</h2>
                            <div class="w-12 h-0.5 mx-auto mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
                        </div>

                        <div class="text-center mb-6 hidden md:block">
                            <h2 class="text-2xl font-bold" style="font-family: var(--font-header1); color: var(--color-primary1);">Forgot Password</h2>
                            <div class="w-12 h-0.5 mx-auto mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
                            <p class="text-gray-500 mt-2 text-sm" style="font-family: var(--font-body1);">Enter your email to reset password</p>
                        </div>

                        <!-- Success Message -->
                        @if (session('status'))
                            <div class="mb-6 p-3 rounded-lg text-center" style="background-color: #d1fae5; color: #065f46;">
                                <p class="text-sm font-medium" style="font-family: var(--font-body1);">{{ session('status') }}</p>
                            </div>
                        @endif

                        <form method="POST" action="/forgot-password" class="space-y-5">
                            @csrf

                            <!-- Email Field -->
                            <div>
                                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: var(--color-primary1);">Email Address <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <input type="email" name="email" id="email" class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-yellow-400/50 transition-all" 
                                        style="font-family: var(--font-body1); background-color: #f9fafb; color: var(--color-primary1);" 
                                        placeholder="you@example.com" value="{{ old('email') }}" required>
                                </div>
                            </div>

                            <!-- Error Messages -->
                            <x-forms.errors name="email" />

                            <!-- Send Reset Link Button -->
                            <button type="submit" class="w-full py-3 font-semibold rounded-full transition-all duration-300 hover:scale-[1.02] hover:shadow-md" 
                                style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                                Send Reset Link
                            </button>

                            <!-- Back to Login Link -->
                            <div class="text-center pt-4">
                                <p class="text-sm text-gray-500" style="font-family: var(--font-body1);">
                                    Remember your password? 
                                    <a href="/login" class="font-semibold hover:underline" style="color: var(--color-primary1);">Back to Login</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>