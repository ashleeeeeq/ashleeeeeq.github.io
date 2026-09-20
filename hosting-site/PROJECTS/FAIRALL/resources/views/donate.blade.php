<x-layout title="Donate">
    <section class="py-24 md:py-32 px-6" style="background: linear-gradient(180deg, #f9fafb 0%, #ffffff 100%);">
        <div class="max-w-7xl mx-auto grid gap-10 lg:grid-cols-[1.15fr_0.85fr] items-start">
            <div class="space-y-6">
                <div>
                    <p class="text-sm uppercase tracking-[0.24em] text-gray-500" style="font-family: var(--font-body1);">
                        Support FAIRALL</p>
                    <h1 class="mt-3 text-4xl md:text-6xl font-bold"
                        style="font-family: var(--font-header1); color: var(--color-primary1);">Make a Donation</h1>
                    <div class="w-20 h-1 mt-4 rounded-full" style="background-color: var(--color-accent1);"></div>
                </div>

                <p class="max-w-2xl text-lg text-gray-600 leading-relaxed" style="font-family: var(--font-body1);">
                    Support our programs with a one-time donation or a recurring PayPal subscription. Choose a program
                    and complete checkout through a secure donation provider.
                </p>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-2xl p-5 shadow-sm"
                        style="background-color: white; border: 1px solid rgba(15, 23, 42, 0.08);">
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500">One-time Donation</p>
                        <p class="mt-2 text-sm text-gray-600">Pay via Xendit or PayPal for a single contribution.</p>
                    </div>
                    <div class="rounded-2xl p-5 shadow-sm"
                        style="background-color: white; border: 1px solid rgba(15, 23, 42, 0.08);">
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500">PayPal Subscription</p>
                        <p class="mt-2 text-sm text-gray-600">Set up recurring support through PayPal billing.</p>
                    </div>
                </div>

                <div class="rounded-3xl p-6 md:p-8"
                    style="background-color: white; border: 1px solid rgba(15, 23, 42, 0.08); box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);">
                    <h2 class="text-2xl md:text-3xl font-bold"
                        style="font-family: var(--font-header1); color: var(--color-primary1);">Why donate here?</h2>
                    <p class="mt-5 space-y-3 text-gray-700" style="font-family: var(--font-body1);">At Fairplay, we
                        believe everyone deserves a fair chance and that no one’s future should be defined by where they
                        were born. Our mission is to create a fairer world by leveling the playing field so every child
                        has the opportunity to learn, play, and grow.</p>
                    <p class="mt-5 space-y-3 text-gray-700" style="font-family: var(--font-body1);">To bring this
                        mission to life, we operate the Fairplay Youth Center, Fairplay Cafe, and the Payatas Sports
                        Center. Through mentoring, education, sports, and community support, we help develop physical
                        and mental well-being, strengthen social and emotional support systems, and open doors to
                        financial and academic opportunities. By taking a holistic approach, we aim to break the cycle
                        of poverty and empower our community for the long term.</p>
                </div>
            </div>

            <div class="rounded-3xl p-6 md:p-8 sticky top-28"
                style="background-color: white; border: 1px solid rgba(15, 23, 42, 0.08); box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);">
                <h2 class="text-2xl font-bold" style="font-family: var(--font-header1); color: var(--color-primary1);">
                    Start your donation</h2>
                <p class="mt-2 text-sm text-gray-500">Choose a program, amount, and payment type.</p>

                @if (auth()->check())
                    <div class="mt-5 rounded-2xl p-4"
                        style="background-color: #f9fafb; border: 1px solid rgba(15, 23, 42, 0.08);">
                        <p class="text-sm font-semibold" style="color: var(--color-primary1);">Signed in as
                            {{ auth()->user()->display_name }}</p>
                        <p class="mt-1 text-sm text-gray-600">We’ll attach payment records to your donor profile.</p>
                    </div>
                @endif

                <div class="mt-6 space-y-6" data-donation-root>
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">Donation Frequency</label>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <label
                                class="flex cursor-pointer items-start gap-3 rounded-2xl p-4 border-2 border-[rgba(15,23,42,0.08)] has-checked:border-accent1"
                                style="background-color: #fff;">
                                <input type="radio" name="donation_frequency_choice" value="one_time" class="mt-1"
                                    checked>
                                <span>
                                    <span class="block font-semibold text-slate-900">One-time</span>
                                    <span class="block text-sm text-gray-500">Xendit or PayPal checkout</span>
                                </span>
                            </label>
                            <label
                                class="flex cursor-pointer items-start gap-3 rounded-2xl p-4 border-2 border-[rgba(15,23,42,0.08)] has-checked:border-accent1"
                                style="background-color: #fff;">
                                <input type="radio" name="donation_frequency_choice" value="subscription"
                                    class="mt-1">
                                <span>
                                    <span class="block font-semibold text-slate-900">Subscription</span>
                                    <span class="block text-sm text-gray-500">PayPal recurring support</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <form id="one-time-donation-form" class="space-y-5" action="{{ route('donate.checkout') }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="donation_frequency" value="one_time">

                        <div data-mode="one-time">
                            <div class="mt-5">
                                <label class="block mb-2 text-sm font-semibold text-gray-700">Payment Gateway <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-2xl p-4 border-2 border-[rgba(15,23,42,0.08)] has-checked:border-accent1"
                                        style="background-color: #fff;">
                                        <input type="radio" name="gateway" value="xendit" class="mt-1" checked
                                            required>
                                        <span>
                                            <span class="block font-semibold text-slate-900">Xendit</span>
                                            <span class="block text-sm text-gray-500">Cards / Bank transfer /
                                                E-wallet</span>
                                        </span>
                                    </label>
                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-2xl p-4 border-2 border-[rgba(15,23,42,0.08)] has-checked:border-accent1"
                                        style="background-color: #fff;">
                                        <input type="radio" name="gateway" value="paypal" class="mt-1" required>
                                        <span>
                                            <span class="block font-semibold text-slate-900">PayPal</span>
                                            <span class="block text-sm text-gray-500">Cards / PayPal balance</span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div class="mt-5">
                                <label class="block mb-2 text-sm font-semibold text-gray-700">Program</label>
                                <select name="program_id"
                                    class="w-full rounded-2xl px-4 py-3 bg-white border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-accent1">
                                    <option value="">No Program</option>
                                    @foreach ($programs as $program)
                                        <option value="{{ $program->id }}">{{ $program->program_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mt-5">
                                <label class="block mb-2 text-sm font-semibold text-gray-700">Amount <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                                <input type="number" step="0.01" min="1" name="amount"
                                    class="w-full rounded-2xl px-4 py-3 bg-white border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-accent1"
                                    placeholder="50.00" required>
                            </div>

                            <div class="mt-5">
                                <label class="block mb-2 text-sm font-semibold text-gray-700">Message / Purpose
                                    (optional)</label>
                                <textarea name="description" rows="4"
                                    class="w-full rounded-2xl px-4 py-3 bg-white border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-accent1"
                                    placeholder="Tell us how you'd like this donation used."></textarea>
                            </div>

                            <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4"
                                data-donation-privacy-form>
                                <p class="text-sm font-semibold text-gray-700">Receipt preference</p>
                                <p class="mt-1 text-xs text-gray-500">Choose whether we should email a receipt or keep this donation anonymous.</p>

                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 has-checked:border-accent1">
                                        <input type="radio" name="privacy_choice" value="receipt"
                                            class="mt-1" data-privacy-choice>
                                        <span>
                                            <span class="block font-semibold text-slate-900">Send me a receipt</span>
                                            <span class="block text-sm text-gray-500">We’ll ask for an email address.</span>
                                        </span>
                                    </label>
                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 has-checked:border-accent1">
                                        <input type="radio" name="privacy_choice" value="anonymous"
                                            class="mt-1" data-privacy-choice checked>
                                        <span>
                                            <span class="block font-semibold text-slate-900">Donate anonymously</span>
                                            <span class="block text-sm text-gray-500">No receipt email will be sent.</span>
                                        </span>
                                    </label>
                                </div>

                                <input type="hidden" name="send_receipt" value="" data-send-receipt-field>
                                <input type="hidden" name="anonymous" value="1" data-anonymous-field>

                                <div class="mt-4" data-receipt-email-field hidden>
                                    <label class="block mb-2 text-sm font-semibold text-gray-700">Receipt email</label>
                                    <input type="email" name="payer_email"
                                        class="w-full rounded-2xl px-4 py-3 bg-white border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-accent1"
                                        placeholder="your@email.com">
                                </div>

                                <div class="mt-4 rounded-2xl border border-accent1/20 bg-accent1/5 p-4 text-sm text-slate-700"
                                    data-claim-account-note hidden>
                                    <p class="font-semibold text-slate-900">Provisional donor account</p>
                                    <p class="mt-1">Selecting this option will create a provisional donor account tied to this email. You can claim it later to view your donation history, manage subscriptions, and view donor impact.</p>
                                </div>
                            </div>

                            <div class="rounded-2xl p-4 text-sm text-gray-600 mt-5"
                                style="background-color: #f9fafb; border: 1px solid rgba(15, 23, 42, 0.08);">
                                <p class="font-semibold text-slate-900">What happens next?</p>
                                <p class="mt-1">You’ll be redirected to the provider to complete checkout. We’ll
                                    generate a receipt after the donation clears.</p>
                            </div>

                            <button type="submit"
                                class="mt-5 w-full rounded-full px-6 py-3 text-sm font-semibold transition-transform duration-200 hover:scale-[1.01]"
                                style="background-color: var(--color-accent1); color: var(--color-primary1);">
                                Continue to Donation
                            </button>
                        </div>
                    </form>

                    <div id="subscription-plan-section" class="grid gap-4 lg:grid-cols-1" hidden>
                        <div class="rounded-3xl border border-[rgba(15,23,42,0.08)] bg-amber-50 p-5 text-sm text-amber-900 shadow-sm">
                            <p class="font-semibold">Provisional donor account</p>
                            <p class="mt-1">If you’re not registered yet, we’ll automatically create a provisional donor account using the email from your PayPal checkout. You can claim it later to view your donation history, manage subscriptions, and track your impact.</p>
                        </div>
                        @foreach ($subscriptionPlans as $planKey => $plan)
                            <form action="{{ route('donate.checkout') }}" method="POST"
                                class="rounded-3xl p-6 border border-[rgba(15,23,42,0.08)] shadow-sm flex flex-col"
                                style="background-color: white;">
                                @csrf
                                <input type="hidden" name="donation_frequency" value="subscription">
                                <input type="hidden" name="subscription_plan" value="{{ $planKey }}">
                                <input type="hidden" name="gateway" value="paypal">

                                <div class="flex-1 flex flex-col gap-3">
                                    <div class="flex items-center justify-between gap-3">
                                        <h3 class="text-xl font-bold"
                                            style="font-family: var(--font-header1); color: var(--color-primary1);">
                                            {{ $plan['name'] }}</h3>
                                    </div>
                                    <p class="mt-3 text-sm leading-6 text-gray-600">{{ $plan['description'] }}</p>
                                    <div class="mt-auto rounded-2xl p-4"
                                        style="background-color: #f9fafb; border: 1px solid rgba(15, 23, 42, 0.08);">
                                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500">Price</p>
                                        <p class="mt-1 text-2xl font-bold text-slate-900">
                                            {{ number_format($plan['amount']) }} PHP<span
                                                class="text-sm font-medium text-gray-500">/month</span></p>
                                        <p class="mt-2 text-sm text-gray-600">Program:
                                            {{ $plan['program_name'] ?? 'No program' }}</p>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="mt-6 w-full rounded-full px-6 py-3 text-sm font-semibold transition-transform duration-200 hover:scale-[1.01]"
                                    style="background-color: var(--color-accent1); color: var(--color-primary1);">
                                    Subscribe
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
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
            var privacyForms = root.querySelectorAll('[data-donation-privacy-form]');

            var syncPrivacyForm = function(form) {
                var selected = form.querySelector('input[name="privacy_choice"]:checked')?.value || 'anonymous';
                var receiptField = form.querySelector('[data-send-receipt-field]');
                var anonymousField = form.querySelector('[data-anonymous-field]');
                var emailField = form.querySelector('[data-receipt-email-field]');
                var emailInput = form.querySelector('input[name="payer_email"]');
                var claimNote = form.querySelector('[data-claim-account-note]');

                if (receiptField) {
                    receiptField.value = selected === 'receipt' ? '1' : '';
                }

                if (anonymousField) {
                    anonymousField.value = selected === 'anonymous' ? '1' : '';
                }

                if (emailField) {
                    emailField.hidden = selected !== 'receipt';
                }

                if (emailInput) {
                    emailInput.required = selected === 'receipt';
                    if (selected !== 'receipt') {
                        emailInput.value = '';
                    }
                }

                if (claimNote) {
                    claimNote.hidden = selected !== 'receipt';
                }
            };

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

                privacyForms.forEach(function(form) {
                    syncPrivacyForm(form);
                });
            };

            radios.forEach(function(radio) {
                radio.addEventListener('change', syncView);
            });

            privacyForms.forEach(function(form) {
                form.querySelectorAll('input[name="privacy_choice"]').forEach(function(choice) {
                    choice.addEventListener('change', syncView);
                });
            });

            syncView();
        });
    </script>
    @if (session('status'))
        <div id="status-modal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-300">
            <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
            <div
                class="relative z-10 w-full max-w-md rounded-3xl border border-green-200 bg-white p-6 shadow-2xl transform scale-95 transition-transform duration-300">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100 text-green-700">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            class="h-6 w-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-green-600">Donation Complete
                        </p>
                        <p class="mt-2 text-base leading-6 text-slate-700">{{ session('status') }}</p>
                    </div>
                    <button id="status-modal-close"
                        class="rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                        aria-label="Close modal">&times;</button>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modal = document.getElementById('status-modal');
                var closeButton = document.getElementById('status-modal-close');
                if (!modal) return;

                requestAnimationFrame(function() {
                    modal.classList.remove('opacity-0', 'pointer-events-none');
                    modal.classList.add('opacity-100');
                    modal.querySelector('.relative')?.classList.remove('scale-95');
                });

                var dismiss = function() {
                    modal.classList.add('opacity-0', 'pointer-events-none');
                    modal.querySelector('.relative')?.classList.add('scale-95');
                    setTimeout(function() {
                        modal.remove();
                    }, 300);
                };

                closeButton?.addEventListener('click', dismiss);
                modal.addEventListener('click', function(event) {
                    if (event.target === modal || event.target.classList.contains('absolute')) {
                        dismiss();
                    }
                });
                setTimeout(dismiss, 6000);
            });
        </script>
    @endif
</x-layout>
