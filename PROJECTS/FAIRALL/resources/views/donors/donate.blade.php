<x-dashboardlayout name="{{ $name }}" title="Donate">
    <div class="mb-8 space-y-6 space-y-6">
        <!-- Header Section -->
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Support FAIRALL</p>
            <h1 class="mt-2 text-2xl md:text-3xl font-bold tracking-tight" style="font-family: var(--font-header1); color: var(--color-white);">Make a Donation</h1>
            <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">Choose a program and complete checkout through a secure donation provider.</p>
            <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
        </div>

        <!-- Donation Type Cards -->
        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-2xl p-5 transition-all duration-300 hover:scale-[1.02] hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
                style="background: linear-gradient(135deg, rgba(255,204,51,0.08), rgba(255,255,255,0.02)); border: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                        <svg class="w-5 h-5" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white" style="font-family: var(--font-header1);">One-time Donation</p>
                        <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Pay via Xendit or PayPal for a single contribution.</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl p-5 transition-all duration-300 hover:scale-[1.02] hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
                style="background: linear-gradient(135deg, rgba(16,185,129,0.08), rgba(255,255,255,0.02)); border: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: rgba(16,185,129,0.15);">
                        <svg class="w-5 h-5" style="color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white" style="font-family: var(--font-header1);">PayPal Subscription</p>
                        <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Set up recurring support through PayPal billing.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signed In Info -->
        @if (auth()->check())
            <div class="rounded-2xl p-4 flex items-center gap-3" style="background-color: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2);">
                <svg class="w-5 h-5 flex-shrink-0" style="color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium" style="color: white;">Signed in as <span style="color: #10b981;">{{ $name }}</span></p>
                    <p class="text-xs" style="color: rgba(255,255,255,0.5);">Your donation will be linked to your donor profile.</p>
                </div>
            </div>
        @endif

        <!-- Main Donation Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                        <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Donation Details</h2>
                        <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Complete the form below to proceed</p>
                    </div>
                </div>
            </div>

            <div class="p-6" data-donation-root>
                <div class="space-y-5">
                    <!-- Donation Frequency Selection -->
                    <div>
                        <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Donation Frequency</label>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl p-4 transition-all duration-200 hover:bg-white/5"
                                style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                                <input type="radio" name="donation_frequency_choice" value="one_time" class="w-4 h-4 accent-accent1" checked>
                                <div>
                                    <span class="block font-semibold text-white">One-time</span>
                                    <span class="block text-xs" style="color: rgba(255,255,255,0.5);">Xendit or PayPal checkout</span>
                                </div>
                            </label>
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl p-4 transition-all duration-200 hover:bg-white/5"
                                style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                                <input type="radio" name="donation_frequency_choice" value="subscription" class="w-4 h-4 accent-accent1">
                                <div>
                                    <span class="block font-semibold text-white">Subscription</span>
                                    <span class="block text-xs" style="color: rgba(255,255,255,0.5);">PayPal recurring support</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- One-time Donation Form -->
                    <form id="one-time-donation-form" class="space-y-5" action="{{ route('donate.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="donation_frequency" value="one_time">

                        <div data-mode="one-time" class="space-y-5">
                            <!-- Payment Gateway Selection -->
                            <div>
                                <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Payment Gateway <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <label class="flex cursor-pointer items-center gap-3 rounded-xl p-4 transition-all duration-200 hover:bg-white/5"
                                        style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                                        <input type="radio" name="gateway" value="xendit" class="w-4 h-4 accent-accent1" checked required>
                                        <div>
                                            <span class="block font-semibold text-white">Xendit</span>
                                            <span class="block text-xs" style="color: rgba(255,255,255,0.5);">Cards / Bank transfer / E-wallet</span>
                                        </div>
                                    </label>
                                    <label class="flex cursor-pointer items-center gap-3 rounded-xl p-4 transition-all duration-200 hover:bg-white/5"
                                        style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                                        <input type="radio" name="gateway" value="paypal" class="w-4 h-4 accent-accent1" required>
                                        <div>
                                            <span class="block font-semibold text-white">PayPal</span>
                                            <span class="block text-xs" style="color: rgba(255,255,255,0.5);">Card or PayPal balance</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Program Selection -->
                            <div>
                                <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Program</label>
                                <div class="relative">
                                    <select name="program_id"
                                        class="w-full rounded-xl px-4 py-2.5 appearance-none transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1 cursor-pointer"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">
                                        <option value="" style="background-color: var(--color-primary1); color: white;">No Program</option>
                                        @foreach ($programs as $program)
                                            <option value="{{ $program->id }}" style="background-color: var(--color-primary1); color: white;">{{ $program->program_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Amount -->
                            <div>
                                <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Amount (PHP) <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/50">₱</span>
                                    <input type="number" step="0.01" min="1" name="amount"
                                        class="w-full rounded-xl px-4 py-2.5 text-primary1 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);"
                                        placeholder="50.00" required>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Message / Purpose <span style="color: rgba(255,255,255,0.4);">(optional)</span></label>
                                <textarea name="description" rows="3"
                                    class="w-full rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1 resize-y"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;"
                                    placeholder="Tell us how you'd like this donation used."></textarea>
                            </div>

                            <!-- Info Box -->
                            <div class="rounded-xl p-4" style="background-color: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.2);">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" style="color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold" style="color: white;">What happens next?</p>
                                        <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.6);">You'll be redirected to the provider to complete checkout. We'll generate a receipt after the donation clears.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit"
                                class="w-full rounded-xl px-6 py-3 text-sm font-semibold transition-all duration-200 hover:scale-[1.02] hover:shadow-lg"
                                style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                                Continue to Donation
                            </button>
                        </div>
                    </form>

                    <!-- Subscription Plans Section -->
                    <div id="subscription-plan-section" class="grid gap-4 md:grid-cols-3" hidden>
                        @foreach ($subscriptionPlans as $planKey => $plan)
                            <form action="{{ route('donate.checkout') }}" method="POST"
                                class="rounded-2xl p-5 transition-all duration-300 hover:scale-[1.02] hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)] flex flex-col"
                                style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                                @csrf
                                <input type="hidden" name="donation_frequency" value="subscription">
                                <input type="hidden" name="subscription_plan" value="{{ $planKey }}">
                                <input type="hidden" name="gateway" value="paypal">

                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-white mb-2" style="font-family: var(--font-header1);">{{ $plan['name'] }}</h3>
                                    <p class="text-sm mb-4" style="color: rgba(255,255,255,0.6);">{{ $plan['description'] }}</p>
                                    
                                    <div class="rounded-xl p-4 mb-4" style="background-color: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.06);">
                                        <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: rgba(255,255,255,0.5);">Price</p>
                                        <p class="text-2xl font-bold text-white" style="font-family: var(--font-header1);">
                                            ₱{{ number_format($plan['amount']) }}<span class="text-sm font-medium" style="color: rgba(255,255,255,0.5);">/month</span>
                                        </p>
                                        <p class="text-xs mt-2" style="color: rgba(255,255,255,0.5);">
                                            Program: {{ $plan['program_name'] ?? 'No program' }}
                                        </p>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="w-full rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-200 hover:scale-[1.02]"
                                    style="background-color: var(--color-accent1); color: var(--color-primary1);">
                                    Subscribe
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var root = document.querySelector('[data-donation-root]');
            if (!root) {
                return;
            }

            var radios = root.querySelectorAll('input[name="donation_frequency_choice"]');
            var oneTimeForm = document.getElementById('one-time-donation-form');
            var subscriptionSection = document.getElementById('subscription-plan-section');
            var donationFrequencyField = oneTimeForm ? oneTimeForm.querySelector(
                'input[name="donation_frequency"]') : null;

            var syncView = function() {
                var selected = root.querySelector('input[name="donation_frequency_choice"]:checked')?.value ||
                    'one_time';
                var isSubscription = selected === 'subscription';

                if (donationFrequencyField) {
                    donationFrequencyField.value = selected;
                }

                if (oneTimeForm) {
                    oneTimeForm.hidden = isSubscription;
                }

                if (subscriptionSection) {
                    subscriptionSection.hidden = !isSubscription;
                }
            };

            radios.forEach(function(radio) {
                radio.addEventListener('change', syncView);
            });

            syncView();
        });
    </script>
</x-dashboardlayout>