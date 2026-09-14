<x-dashboardlayout name="{{ $name }}" title="Staff Profile">
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight"
                    style="font-family: var(--font-header1); color: var(--color-white);">
                    Staff Profile
                </h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                    {{ $user->display_name ?? ($user->name ?? 'N/A') }} &middot; {{ $user->login_id }}
                </p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
            <div class="flex gap-2">
                <a href="/users/{{ $user->id }}/edit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap"
                    style="background-color: var(--color-accent1); color: var(--color-primary1); font-family: var(--font-body1);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10">
                        </path>
                    </svg>
                    Edit User
                </a>
            </div>
        </div>

        @php $staff = $user->staff; @endphp

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
                    style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                        style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center"
                                style="background-color: rgba(255,204,51,0.15);">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white text-sm sm:text-base"
                                    style="font-family: var(--font-header1);">Personal Information</h3>
                                <p class="text-xs" style="color: rgba(255,255,255,0.5);">Basic details and contact info
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 sm:p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5);">First Name</dt>
                                <dd class="mt-1 text-sm text-white">{{ $staff?->first_name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5);">Middle Name</dt>
                                <dd class="mt-1 text-sm text-white">{{ $staff?->middle_name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5);">Last Name</dt>
                                <dd class="mt-1 text-sm text-white">{{ $staff?->last_name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5);">Contact Number</dt>
                                <dd class="mt-1 text-sm text-white">{{ $staff?->formatted_contact ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5);">Email</dt>
                                <dd class="mt-1 text-sm text-white">{{ $user->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5);">Login ID</dt>
                                <dd class="mt-1 text-sm text-white">{{ $user->login_id }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
                    style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                        style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center"
                                style="background-color: rgba(255,204,51,0.15);">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white text-sm sm:text-base"
                                    style="font-family: var(--font-header1);">Employment Details</h3>
                                <p class="text-xs" style="color: rgba(255,255,255,0.5);">Role, department, and position
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 sm:p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5);">Role</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                        style="background-color: {{ $staff?->role === 'administrator' ? 'rgba(239,68,68,0.15)' : 'rgba(59,130,246,0.15)' }}; color: {{ $staff?->role === 'administrator' ? '#f87171' : '#60a5fa' }};">
                                        {{ ucfirst(str_replace('_', ' ', $staff?->role ?? 'Staff')) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5);">Department</dt>
                                <dd class="mt-1 text-sm text-white">
                                    {{ is_object($staff?->department) ? $staff->department->name : $staff?->department ?? 'N/A' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5);">Position</dt>
                                <dd class="mt-1 text-sm text-white">
                                    {{ is_object($staff?->position) ? $staff->position->name : $staff?->position ?? 'N/A' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                @if ($staff?->address)
                    <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
                        style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                            style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center"
                                    style="background-color: rgba(255,204,51,0.15);">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white text-sm sm:text-base"
                                        style="font-family: var(--font-header1);">Address</h3>
                                    <p class="text-xs" style="color: rgba(255,255,255,0.5);">Registered address</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 sm:p-6">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <dt class="text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5);">Address Line</dt>
                                    <dd class="mt-1 text-sm text-white">{{ $staff->address->address_line ?? 'N/A' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5);">City</dt>
                                    <dd class="mt-1 text-sm text-white">{{ $staff->address->city ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5);">Province</dt>
                                    <dd class="mt-1 text-sm text-white">{{ $staff->address->province ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5);">Zip Code</dt>
                                    <dd class="mt-1 text-sm text-white">{{ $staff->address->zip ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5);">Country</dt>
                                    <dd class="mt-1 text-sm text-white">{{ $staff->address->country ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
                    style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                        style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center"
                                style="background-color: rgba(255,204,51,0.15);">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white text-sm sm:text-base"
                                    style="font-family: var(--font-header1);">Account Status</h3>
                                <p class="text-xs" style="color: rgba(255,255,255,0.5);">Authentication &amp; activity
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 sm:p-6 space-y-4">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider mb-2"
                                style="color: rgba(255,255,255,0.5);">Status</dt>
                            <div class="flex items-center gap-1.5">
                                <div class="w-2 h-2 rounded-full"
                                    style="background-color: {{ $user->is_active ? '#10b981' : '#ef4444' }};"></div>
                                <span class="text-sm"
                                    style="color: {{ $user->is_active ? '#34d399' : '#f87171' }};">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider mb-2"
                                style="color: rgba(255,255,255,0.5);">Email Verification</dt>
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                style="background-color: {{ $user->email_verified_at ? 'rgba(59,130,246,0.15)' : 'rgba(255,255,255,0.1)' }}; color: {{ $user->email_verified_at ? '#60a5fa' : 'rgba(255,255,255,0.5)' }};">
                                {{ $user->email_verified_at ? 'Verified' : 'Pending' }}
                            </span>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider mb-2"
                                style="color: rgba(255,255,255,0.5);">Member Since</dt>
                            <dd class="text-sm text-white">{{ $user->created_at->format('F d, Y') }}</dd>
                        </div>
                        @if ($user->email_verified_at)
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider mb-2"
                                    style="color: rgba(255,255,255,0.5);">Verified At</dt>
                                <dd class="text-sm text-white">{{ $user->email_verified_at->format('F d, Y') }}</dd>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($staff?->created_at || $staff?->updated_at)
                    <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
                        style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                            style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center"
                                    style="background-color: rgba(255,204,51,0.15);">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white text-sm sm:text-base"
                                        style="font-family: var(--font-header1);">Timeline</h3>
                                    <p class="text-xs" style="color: rgba(255,255,255,0.5);">Record creation and
                                        updates</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 sm:p-6 space-y-4">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider mb-2"
                                    style="color: rgba(255,255,255,0.5);">Created At</dt>
                                <dd class="text-sm text-white mb-2">
                                    {{ $staff?->created_at?->format('F d, Y \a\t h:i A') ?? 'N/A' }}</dd>
                                <dt class="text-xs font-semibold uppercase tracking-wider mb-2"
                                    style="color: rgba(255,255,255,0.5);">Updated At</dt>
                                <dd class="text-sm text-white">
                                    {{ $staff?->updated_at?->format('F d, Y \a\t h:i A') ?? 'N/A' }}</dd>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Back Button -->
        <div class="flex justify-end">
            <a href="/users"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold transition-all duration-300 hover:bg-white/10"
                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);"
                onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
                onmouseout="this.style.backgroundColor='transparent'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
                Back to Users
            </a>
        </div>
    </div>
</x-dashboardlayout>
