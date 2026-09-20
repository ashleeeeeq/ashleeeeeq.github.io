<x-layout title="Reset Password">
    <form method="POST" action="/reset-password">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}" />

        <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-xs border p-4 m-auto">
            <legend class="fieldset-legend">Reset Password</legend>

            <label class="label">Email <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
            <input type="email" name="email" id="email" class="input" placeholder="Email" value="{{ old('email', $email) }}" required />
            <x-forms.errors name="email" />

            <label class="label">New Password <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
            <input type="password" name="password" id="password" class="input" placeholder="New Password" required />
            <x-forms.errors name="password" />
            <ul id="password-requirements" class="mt-2 space-y-0.5"></ul>
            <script>document.addEventListener('DOMContentLoaded',function(){initPasswordRequirements('password','password-requirements',{failedClass:'text-primary1'})});</script>

            <label class="label">Confirm New Password <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="input" placeholder="Confirm Password" required />
            <ul id="confirm-password-requirements" class="mt-2 space-y-0.5"></ul>
            <script>document.addEventListener('DOMContentLoaded',function(){initPasswordMatch('password','password_confirmation','confirm-password-requirements',{failedClass:'text-primary1'})});</script>

            <button type="submit" class="btn btn-neutral mt-4">Reset Password</button>
        </fieldset>
    </form>
</x-layout>
