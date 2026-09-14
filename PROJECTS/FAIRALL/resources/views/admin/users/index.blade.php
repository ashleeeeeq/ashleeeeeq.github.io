<x-dashboardlayout name="{{ $name }}" title="User Accounts">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">User Accounts</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Manage staff members and beneficiaries</p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
            <a href="/users/create" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap" style="background-color: var(--color-accent1); color: var(--color-primary1); font-family: var(--font-body1);">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"></path>
                </svg>
                Create Staff User
            </a>
        </div>


        <!-- Success Message -->
        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl flex items-center gap-3" style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3);">
                <svg class="w-5 h-5 shrink-0" style="color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm" style="color: #10b981; font-family: var(--font-body1);">{{ session('status') }}</span>
            </div>
        @endif


        <div class="w-full">


            <div class="overflow-x-auto overflow-y-hidden mb-6">
                <div class="flex gap-1 border-b min-w-max" style="border-color: rgba(255,255,255,0.08);">
                    <button type="button" onclick="switchTab('staff')" id="staffTabBtn" 
                        class="tab-btn-active inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"></path>
                        </svg>
                        Staff
                        <span class="tab-count px-2 py-0.5 text-xs rounded-full" style="background-color: var(--color-accent1); color: var(--color-primary1);">{{ $staffUsers->total() }}</span>
                    </button>
                    <button type="button" onclick="switchTab('beneficiaries')" id="beneficiariesTabBtn" 
                        class="tab-btn-inactive inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 1 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"></path>
                        </svg>
                        Beneficiaries
                        <span class="tab-count px-2 py-0.5 text-xs rounded-full" style="background-color: rgba(255,255,255,0.2); color: rgba(255,255,255,0.8);">{{ $beneficiaryUsers->total() }}</span>
                    </button>
                    <button type="button" onclick="switchTab('donors')" id="donorsTabBtn" 
                        class="tab-btn-inactive inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Donors
                        <span class="tab-count px-2 py-0.5 text-xs rounded-full" style="background-color: rgba(255,255,255,0.2); color: rgba(255,255,255,0.8);">{{ $donorUsers->total() }}</span>
                    </button>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="rounded-2xl mb-6 p-4" style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                <form method="GET" action="" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                    <input type="hidden" name="tab" value="{{ $activeTab }}">
                    <div>
                    <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Search</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-primary1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        <input type="text" name="search" placeholder="Search by login ID, name, or email..." value="{{ request('search') }}"
                               class="w-full rounded-xl pl-10 pr-4 py-2 text-sm text-primary1"
                               style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                    </div>
                </div>
                    <div class="flex gap-2">
                        <button type="submit"
                                class="flex-1 px-5 py-2 text-sm font-semibold rounded-xl transition-all duration-200 hover:scale-[1.02]"
                                style="background-color: var(--color-accent1); color: var(--color-primary1);">
                            Filter
                        </button>
                        <a href="/users"
                           class="flex-1 px-5 py-2 text-sm font-semibold rounded-xl transition-all duration-200 text-center"
                           style="background-color: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1);">
                            Clear
                        </a>
                    </div>
                </form>
            </div>


            <div id="staff-tab" class="tab-content" style="display: none;">
                <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                        <div class="flex items-center justify-between gap-4 flex-wrap">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="font-semibold text-white text-sm sm:text-base" style="font-family: var(--font-header1);">Staff Users</h2>
                                    <p class="text-xs" style="color: rgba(255,255,255,0.5);">All staff accounts in the system</p>
                                </div>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded-full whitespace-nowrap" style="background-color: rgba(255,255,255,0.08); color: rgba(255,255,255,0.6); font-family: var(--font-body1);">{{ $staffUsers->total() }} staff</span>
                        </div>
                    </div>
                    <div class="px-4 sm:px-6 pb-3">
                        <x-bulk-actions-bar bulkUrl="{{ route('users.bulk-destroy') }}" tableId="staffUsers" label="users" />
                    </div>

                    <div class="overflow-x-auto">
                        <div class="p-6">
                            <table class="w-full min-w-225" data-bulk-table="staffUsers">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <th class="text-center px-3 sm:px-4 py-2 sm:py-3">
                                        <input type="checkbox" data-select-all class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                                    </th>
                                    <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Login ID</th>
                                    <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Name</th>
                                    <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Email</th>
                                    <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Role</th>
                                    <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Department</th>
                                    <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Status</th>
                                    <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staffUsers as $index => $user)
                                    <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                        <td class="px-3 sm:px-4 py-3 align-middle text-center">
                                            <input type="checkbox" value="{{ $user->id }}" data-row-checkbox @if(auth()->id() === $user->id) disabled title="You cannot delete your own account" @endif class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed">
                                        </td>
                                        <td class="px-3 sm:px-4 py-3 align-middle">
                                            <span class="text-xs sm:text-sm whitespace-nowrap" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $user->login_id }}</span>
                                        </td>
                                        <td class="px-3 sm:px-4 py-3 align-middle">
                                            <span class="text-xs sm:text-sm font-medium text-white block max-w-37.5 truncate" style="font-family: var(--font-body1);" title="{{ $user->display_name ?? $user->name ?? 'N/A' }}">
                                                {{ $user->display_name ?? $user->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-3 sm:px-4 py-3 align-middle">
                                            <span class="text-xs sm:text-sm block max-w-45 truncate" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);" title="{{ $user->email }}">
                                                {{ $user->email }}
                                            </span>
                                        </td>
                                        <td class="px-3 sm:px-4 py-3 align-middle">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full whitespace-nowrap" style="background-color: {{ $user->staff?->role === 'administrator' ? 'rgba(239,68,68,0.15)' : 'rgba(59,130,246,0.15)' }}; color: {{ $user->staff?->role === 'administrator' ? '#f87171' : '#60a5fa' }}; font-family: var(--font-body1);">
                                                {{ ucfirst(str_replace('_', ' ', $user->staff?->role ?? 'Staff')) }}
                                            </span>
                                        </td>
                                        <td class="px-3 sm:px-4 py-3 align-middle">
                                            <span class="text-xs sm:text-sm block max-w-30 truncate" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);" title="{{ is_object($user->staff?->department) ? $user->staff?->department->name : ($user->staff?->department ?? 'N/A') }}">
                                                {{ is_object($user->staff?->department) ? $user->staff?->department->name : ($user->staff?->department ?? 'N/A') }}
                                            </span>
                                        </td>
                                        <td class="px-3 sm:px-4 py-3 align-middle">
                                            <div class="flex flex-nowrap items-center gap-2">
                                               <div class="flex items-center gap-1.5">
                                                    <div class="w-2 h-2 rounded-full" style="background-color: {{ $user->is_active ? '#10b981' : '#ef4444' }};"></div>
                                                    <span class="text-xs" style="color: {{ $user->is_active ? '#34d399' : '#f87171' }};">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                                                </div>
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full whitespace-nowrap" style="background-color: {{ $user->email_verified_at ? 'rgba(59,130,246,0.15)' : 'rgba(255,255,255,0.1)' }}; color: {{ $user->email_verified_at ? '#60a5fa' : 'rgba(255,255,255,0.5)' }}; font-family: var(--font-body1);">
                                                    {{ $user->email_verified_at ? 'Verified' : 'Pending' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-3 sm:px-4 py-3 align-middle">
                                            <div class="flex items-center justify-center gap-1 sm:gap-2">
                                                <a href="/users/{{ $user->id }}" class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-white/10 hover:scale-110" title="View">
                                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: rgba(255,255,255,0.6);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"></path>
                                                    </svg>
                                                </a>
                                                <a href="/users/{{ $user->id }}/edit" class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-white/10 hover:scale-110" title="Edit">
                                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: rgba(255,255,255,0.6);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                                                    </svg>
                                                </a>
                                                @if (auth()->id() !== $user->id)
                                                    <button type="button" class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-red-500/20 hover:scale-110" title="Delete" onclick="document.getElementById('deleteUserModal_{{ $user->id }}').showModal()">
                                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                                                        </svg>
                                                    </button>


                                                    <x-confirm-dialog
                                                        id="deleteUserModal_{{ $user->id }}"
                                                        title="Delete User"
                                                        message="Are you sure you want to delete <strong>{{ $user->display_name }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
                                                        deleteUrl="/users/{{ $user->id }}" />
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-12 text-center">
                                            <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                            </svg>
                                            <p class="text-sm" style="color: rgba(255,255,255,0.4); font-family: var(--font-body1);">No staff users found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
                @if ($staffUsers->hasPages())
                    <div class="flex justify-center pt-6 mt-4">
                        {{ $staffUsers->links('pagination.custom') }}
                    </div>
                @endif
            </div>


            <div id="beneficiaries-tab" class="tab-content" style="display: none;">
                <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                        <div class="flex items-center justify-between gap-4 flex-wrap">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 1 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="font-semibold text-white text-sm sm:text-base" style="font-family: var(--font-header1);">Beneficiary Users</h2>
                                    <p class="text-xs" style="color: rgba(255,255,255,0.5);">All beneficiary accounts in the system</p>
                                </div>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded-full whitespace-nowrap" style="background-color: rgba(255,255,255,0.08); color: rgba(255,255,255,0.6); font-family: var(--font-body1);">{{ $beneficiaryUsers->total() }} beneficiaries</span>
                        </div>
                    </div>
                    <div class="px-4 sm:px-6 pb-3">
                        <x-bulk-actions-bar bulkUrl="{{ route('users.bulk-destroy') }}" tableId="beneficiaryUsers" label="users" />
                    </div>

                    <div class="overflow-x-auto">
                        <div class="p-6">
                            <table class="w-full min-w-225" data-bulk-table="beneficiaryUsers">
                                <thead>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                        <th class="text-center px-3 sm:px-4 py-2 sm:py-3">
                                            <input type="checkbox" data-select-all class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                                        </th>
                                        <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Login ID</th>
                                        <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Name</th>
                                        <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Email</th>
                                        <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Grade Level</th>
                                        <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Programs</th>
                                        <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Status</th>
                                        <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($beneficiaryUsers as $index => $user)
                                        <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                            <td class="px-3 sm:px-4 py-3 align-middle text-center">
                                                <input type="checkbox" value="{{ $user->id }}" data-row-checkbox @if(auth()->id() === $user->id) disabled title="You cannot delete your own account" @endif class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed">
                                            </td>
                                            <td class="px-3 sm:px-4 py-3 align-middle">
                                                <span class="text-xs sm:text-sm whitespace-nowrap" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $user->login_id }}</span>
                                            </td>
                                            <td class="px-3 sm:px-4 py-3 align-middle">
                                                <span class="text-xs sm:text-sm font-medium text-white block max-w-37.5 truncate" style="font-family: var(--font-body1);" title="{{ $user->display_name ?? $user->name ?? 'N/A' }}">
                                                    {{ $user->display_name ?? $user->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="px-3 sm:px-4 py-3 align-middle">
                                                <span class="text-xs sm:text-sm block max-w-45 truncate" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);" title="{{ $user->email }}">
                                                    {{ $user->email }}
                                                </span>
                                            </td>
                                            <td class="px-3 sm:px-4 py-3 align-middle">
                                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full whitespace-nowrap" style="background-color: rgba(59,130,246,0.15); color: #60a5fa; font-family: var(--font-body1);">
                                                    {{ $user->beneficiary?->grade_level ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="px-3 sm:px-4 py-3 align-middle">
                                                @php
                                                    $programs = $user->beneficiary?->programs ?? collect();
                                                @endphp
                                                @if ($programs->count() > 0)
                                                    <div class="flex flex-nowrap gap-1 overflow-x-auto max-w-50">
                                                        @foreach ($programs as $program)
                                                            <span class="inline-flex px-2 py-1 text-xs rounded-full whitespace-nowrap" style="background-color: rgba(255,204,51,0.15); color: var(--color-accent1); font-family: var(--font-body1);">
                                                                {{ $program->program_name ?? 'N/A' }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-xs" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">No Programs</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2 flex-nowrap">
                                                    <div class="flex items-center gap-1.5">
                                                        <div class="w-2 h-2 rounded-full" style="background-color: {{ $user->is_active ? '#10b981' : '#ef4444' }};"></div>
                                                        <span class="text-xs" style="color: {{ $user->is_active ? '#34d399' : '#f87171' }};">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                                                    </div>
                                                    <span class="text-xs px-1.5 py-0.5 rounded-full whitespace-nowrap" style="background-color: rgba(59,130,246,0.15); color: #60a5fa;">Verified</span>
                                                </div>
                                            </td>

                                            <td class="px-3 sm:px-4 py-3 align-middle">
                                                <div class="flex items-center justify-center gap-1 sm:gap-2">
                                                    @if ($user->beneficiary)
                                                        <a href="/beneficiaries/{{ $user->beneficiary->id }}/profile" class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-white/10 hover:scale-110" title="View">
                                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: rgba(255,255,255,0.6);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"></path>
                                                            </svg>
                                                        </a>
                                                        <a href="/users/{{ $user->id }}/edit" class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-white/10 hover:scale-110" title="Edit">
                                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: rgba(255,255,255,0.6);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                                                            </svg>
                                                        </a>
                                                        <button type="button" class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-red-500/20 hover:scale-110" title="Delete" onclick="document.getElementById('deleteBeneficiaryModal_{{ $user->id }}').showModal()">
                                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                                                            </svg>
                                                        </button>

                                                        <x-confirm-dialog
                                                            id="deleteBeneficiaryModal_{{ $user->id }}"
                                                            title="Delete Beneficiary"
                                                            message="Are you sure you want to delete <strong>{{ $user->beneficiary->display_name }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
                                                            deleteUrl="/beneficiaries/{{ $user->beneficiary->id }}" />
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-4 py-12 text-center">
                                                <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                <p class="text-sm" style="color: rgba(255,255,255,0.4); font-family: var(--font-body1);">No beneficiaries found</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @if ($beneficiaryUsers->hasPages())
                    <div class="flex justify-center pt-6 mt-4">
                        {{ $beneficiaryUsers->links('pagination.custom') }}
                    </div>
                @endif
            </div>


            <div id="donors-tab" class="tab-content" style="display: none;">
                @include('admin.users.partials.donor-table')
            </div>
        </div>
    </div>


    <script>
        function switchTab(tabName) {
            const staffTab = document.getElementById('staff-tab');
            const beneficiariesTab = document.getElementById('beneficiaries-tab');
            const donorsTab = document.getElementById('donors-tab');
            const staffBtn = document.getElementById('staffTabBtn');
            const beneficiariesBtn = document.getElementById('beneficiariesTabBtn');
            const donorsBtn = document.getElementById('donorsTabBtn');

            const tabs = [
                [staffTab, staffBtn],
                [beneficiariesTab, beneficiariesBtn],
                [donorsTab, donorsBtn],
            ];

            tabs.forEach(([tab, button]) => {
                if (tab) tab.style.display = 'none';
                if (button) {
                    // Remove all inline styles so CSS can take over
                    button.removeAttribute('style');
                    // Add back the essential styles via class
                    button.className = 'tab-btn-inactive inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 whitespace-nowrap';
                    // Apply base styles that won't conflict with hover
                    button.style.fontFamily = 'var(--font-body1)';
                    button.style.backgroundColor = 'transparent';
                    button.style.color = 'rgba(255,255,255,0.6)';
                    button.style.borderBottom = '2px solid transparent';
                    
                    const countSpan = button.querySelector('.tab-count');
                    if (countSpan) {
                        countSpan.style.backgroundColor = 'rgba(255,255,255,0.2)';
                        countSpan.style.color = 'rgba(255,255,255,0.8)';
                    }
                }
            });

            const activeTab = document.getElementById(`${tabName}-tab`);
            const activeButton = document.getElementById(`${tabName}TabBtn`);

            if (activeTab) activeTab.style.display = 'block';
            if (activeButton) {
                activeButton.className = 'tab-btn-active inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 whitespace-nowrap';
                activeButton.style.fontFamily = 'var(--font-body1)';
                activeButton.style.backgroundColor = 'transparent';
                activeButton.style.color = 'var(--color-accent1)';
                activeButton.style.borderBottom = '2px solid var(--color-accent1)';
                
                const countSpan = activeButton.querySelector('.tab-count');
                if (countSpan) {
                    countSpan.style.backgroundColor = 'var(--color-accent1)';
                    countSpan.style.color = 'var(--color-primary1)';
                }
            }

            const url = new URL(window.location.href);
            url.searchParams.set('tab', tabName);
            window.history.replaceState({}, '', url);
        }


        document.addEventListener('DOMContentLoaded', function() {
            switchTab(@json($activeTab));
        });
    </script>
</x-dashboardlayout>

