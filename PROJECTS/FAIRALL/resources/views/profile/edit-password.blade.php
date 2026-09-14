<x-dashboardlayout title="Change Password">
    <div class="mb-8 space-y-6">
        <!-- Page Header -->
        <div>
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white" style="font-family: var(--font-header1);">Change Password</h1>
                    <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Update your account password.</p>
                </div>
            </div>
            <div class="w-16 h-1 mt-3 rounded-full" style="background-color: var(--color-accent1);"></div>
        </div>

        <!-- Change Password Form -->
        <div class="rounded-2xl p-6 bg-white/5 border border-white/10">
            <form method="POST" action="/profile/password" class="space-y-5">
                @csrf
                @method('POST')

                <!-- Current Password -->
                <div>
                    <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Current Password <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                    <input type="password" name="current_password" id="currentPassword"
                        class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all text-primary1"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                        placeholder="••••••••" required>
                    <x-forms.errors name="current_password" />
                </div>

                <!-- New Password -->
                <div>
                    <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">New Password <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                    <input type="password" name="password" id="newPassword"
                        class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all text-primary1"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                        placeholder="••••••••" required>
                    <x-forms.errors name="password" />
                    <ul id="password-requirements" class="mt-2 space-y-0.5"></ul>
                    <script>document.addEventListener('DOMContentLoaded',function(){initPasswordRequirements('newPassword','password-requirements')});</script>
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Confirm New Password <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                    <input type="password" name="password_confirmation" id="confirmPassword"
                        class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all text-primary1"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                        placeholder="••••••••" required>
                    <x-forms.errors name="password_confirmation" />
                    <ul id="confirm-password-requirements" class="mt-2 space-y-0.5"></ul>
                    <script>document.addEventListener('DOMContentLoaded',function(){initPasswordMatch('newPassword','confirmPassword','confirm-password-requirements')});</script>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-3 pt-4">
                    <button type="submit"
                        class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center gap-2"
                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                        Update Password
                    </button>
                    <a href="/profile"
                        class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] text-center inline-flex items-center gap-2"
                        style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);">Cancel</a>
                </div>
            </form>
        </div>

        <!-- Back to Profile -->
        <div class="text-center pt-2">
            <a href="/profile" class="text-sm transition-all duration-200"
               style="font-family: var(--font-body1); color: rgba(255,255,255,0.5);"
               onmouseover="this.style.color='var(--color-accent1)'"
               onmouseout="this.style.color='rgba(255,255,255,0.5)'">← Back to Profile</a>
        </div>
    </div>
</x-dashboardlayout>
