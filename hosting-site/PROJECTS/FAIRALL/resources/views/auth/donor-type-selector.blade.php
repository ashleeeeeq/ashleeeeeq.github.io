<x-layout title="Sign Up as Donor">
    <div class="min-h-screen max-h-screen w-full relative flex items-center justify-center py-12 px-4 md:px-6">
        <!-- Left Side - Image -->
        <div class="relative z-10 w-full max-w-5xl -mt-20 md:-mt-24">
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
                        <h2 class="text-3xl font-bold mb-3 text-white" style="font-family: var(--font-header1);">Join Us!
                        </h2>
                        <div class="w-12 h-0.5 mx-auto mb-5 rounded-full"
                            style="background-color: var(--color-accent1);"></div>
                        <p class="text-white/80 text-sm max-w-xs" style="font-family: var(--font-body1);">Leveling the
                            playing field.</p>
                    </div>
                </div>

                <!-- Right Side - Sign Up Form -->
                <div class="w-full md:w-3/5 flex items-center justify-center p-8 md:p-10"
                    style="background-color: rgba(255, 255, 255, 0.95);">
                    <div class="w-full max-w-md">
                        <!-- Mobile Logo -->
                        <div class="text-center mb-8 md:hidden">
                            <img src="{{ asset('images/FAIRALL_LOGO.png') }}" alt="FAIRALL Logo"
                                class="h-12 mx-auto mb-3">
                            <h2 class="text-2xl font-bold"
                                style="font-family: var(--font-header1); color: var(--color-primary1);">Create Account
                            </h2>
                            <div class="w-12 h-0.5 mx-auto mt-2 rounded-full"
                                style="background-color: var(--color-accent1);"></div>
                        </div>

                        <div class="text-center mb-6 hidden md:block">
                            <h2 class="text-2xl font-bold"
                                style="font-family: var(--font-header1); color: var(--color-primary1);">Create Donor Account</h2>
                            <div class="w-12 h-0.5 mx-auto mt-2 rounded-full"
                                style="background-color: var(--color-accent1);"></div>
                            <p class="text-gray-500 mt-2 text-sm" style="font-family: var(--font-body1);">Select your account type</p>
                        </div>

                        <form method="GET" action="/register" class="space-y-5" id="donorTypeForm">
                            <!-- Individual Option -->
                            <label class="flex items-start gap-4 p-4 rounded-xl cursor-pointer transition-all duration-200 hover:border-yellow-400" 
                                   style="background-color: #f9fafb; border: 1.5px solid #e5e7eb;">
                                <input type="radio" name="type" value="individual" class="w-4 h-4 mt-0.5 cursor-pointer" style="accent-color: var(--color-accent1);" required />
                                <div class="flex-1">
                                    <p class="font-semibold" style="font-family: var(--font-header1); color: var(--color-primary1);">Individual Donor</p>
                                    <p class="text-sm text-gray-500 mt-1" style="font-family: var(--font-body1);">Register as a person to make donations</p>
                                </div>
                            </label>

                            <!-- Organization Option -->
                            <label class="flex items-start gap-4 p-4 rounded-xl cursor-pointer transition-all duration-200 hover:border-yellow-400" 
                                   style="background-color: #f9fafb; border: 1.5px solid #e5e7eb;">
                                <input type="radio" name="type" value="organization" class="w-4 h-4 mt-0.5 cursor-pointer" style="accent-color: var(--color-accent1);" required />
                                <div class="flex-1">
                                    <p class="font-semibold" style="font-family: var(--font-header1); color: var(--color-primary1);">Organization Donor</p>
                                    <p class="text-sm text-gray-500 mt-1" style="font-family: var(--font-body1);">Register your organization to make group donations</p>
                                </div>
                            </label>

                            <!-- Continue Button -->
                            <button type="submit" class="w-full py-3 font-semibold rounded-full transition-all duration-300 hover:scale-[1.02]"
                                style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                                Continue
                            </button>

                            <!-- Back to Login Link -->
                            <div class="text-center pt-4">
                                <p class="text-sm text-gray-500" style="font-family: var(--font-body1);">
                                    Already have an account?
                                    <a href="/login" class="font-semibold hover:underline"
                                        style="color: var(--color-primary1);">Sign in here</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle form submission with radio selection
        document.getElementById('donorTypeForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const selected = document.querySelector('input[name="type"]:checked');

            if (!selected) {
                alert('Please select an account type to continue.');
                return;
            }

            window.location.href = '/register/' + selected.value;
        });

        // Add visual feedback when radio is selected
        document.querySelectorAll('input[name="type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('label').forEach(label => {
                    label.style.borderColor = '#e5e7eb';
                });
                if (this.checked) {
                    this.closest('label').style.borderColor = 'var(--color-accent1)';
                }
            });
        });
    </script>
</x-layout>