<x-dashboardlayout title="Change Password" :hideChrome="true">
    <div class="w-full max-w-md mx-auto">
        <div class="rounded-2xl p-6 sm:p-8"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-white" style="font-family: var(--font-header1);">Change Your Password</h1>
                <p class="text-sm mt-2" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                    For security, you must change your password before continuing.
                </p>
            </div>

            <form method="POST" action="/force-password-change" class="space-y-5">
                @csrf

                <div>
                    <label class="block mb-2 text-sm font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">
                        New Password <span class="text-xs font-normal" style="color: var(--color-danger);">*</span>
                    </label>
                    <input type="password" name="password" id="password"
                        class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all text-primary1"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                        placeholder="Enter new password" required>
                    <x-forms.errors name="password" />
                    <ul id="password-requirements" class="mt-2 space-y-0.5"></ul>
                    <script>document.addEventListener('DOMContentLoaded',function(){initPasswordRequirements('password','password-requirements')});</script>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">
                        Confirm New Password <span class="text-xs font-normal" style="color: var(--color-danger);">*</span>
                    </label>
                    <input type="password" name="password_confirmation" id="confirmPassword"
                        class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all text-primary1"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                        placeholder="Confirm new password" required>
                    <x-forms.errors name="password_confirmation" />
                    <ul id="confirm-password-requirements" class="mt-2 space-y-0.5"></ul>
                    <script>document.addEventListener('DOMContentLoaded',function(){initPasswordMatch('password','confirmPassword','confirm-password-requirements')});</script>
                </div>

                <button type="submit"
                    class="w-full px-6 py-3 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                    style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                    Change Password
                </button>
            </form>
        </div>
    </div>
</x-dashboardlayout>
