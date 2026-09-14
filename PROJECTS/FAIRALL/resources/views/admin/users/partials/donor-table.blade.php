<div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
        <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-white text-sm sm:text-base" style="font-family: var(--font-header1);">Donor Users</h2>
                    <p class="text-xs" style="color: rgba(255,255,255,0.5);">All donor accounts in the system</p>
                </div>
            </div>
            <span class="text-xs px-2 py-0.5 rounded-full whitespace-nowrap" style="background-color: rgba(255,255,255,0.08); color: rgba(255,255,255,0.6); font-family: var(--font-body1);">{{ $donorUsers->total() }} donors</span>
        </div>
    </div>
    <div class="px-4 sm:px-6 pb-3">
        <x-bulk-actions-bar bulkUrl="{{ route('users.bulk-destroy') }}" tableId="donorUsers" label="users" />
    </div>
    <div class="overflow-x-auto">
        <div class="p-6">
            <table class="w-full min-w-225" data-bulk-table="donorUsers">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                        <th class="text-center px-3 sm:px-4 py-2 sm:py-3">
                            <input type="checkbox" data-select-all class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                        </th>
                        <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Login ID</th>
                        <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Name</th>
                        <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Email</th>
                        <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Status</th>
                        <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($donorUsers as $index => $user)
                        <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                            <td class="px-3 sm:px-4 py-3 align-middle text-center">
                                <input type="checkbox" value="{{ $user->id }}" data-row-checkbox @if(auth()->id() === $user->id) disabled title="You cannot delete your own account" @endif class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed">
                            </td>
                            <td class="px-3 sm:px-4 py-3 align-middle">
                                <span class="text-xs sm:text-sm whitespace-nowrap" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $user->login_id }}</span>
                            </td>
                            <td class="px-3 sm:px-4 py-3 align-middle">
                                <span class="text-xs sm:text-sm font-medium text-white block max-w-37.5 truncate" style="font-family: var(--font-body1);" title="{{ $user->display_name ?? 'N/A' }}">
                                    {{ $user->display_name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-3 sm:px-4 py-3 align-middle">
                                <span class="text-xs sm:text-sm block max-w-45 truncate" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);" title="{{ $user->email }}">
                                    {{ $user->email }}
                                </span>
                            </td>
                            <td class="px-3 sm:px-4 py-3 align-middle">
                                <div class="flex flex-nowrap items-center gap-2">
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-2 h-2 rounded-full" style="background-color: {{ $user->is_active ? '#10b981' : '#ef4444' }};"></div>
                                        <span class="text-xs" style="color: {{ $user->is_active ? '#34d399' : '#f87171' }}; font-family: var(--font-body1);">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                                    </div>
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full whitespace-nowrap" style="background-color: {{ $user->email_verified_at ? 'rgba(59,130,246,0.15)' : 'rgba(255,255,255,0.1)' }}; color: {{ $user->email_verified_at ? '#60a5fa' : 'rgba(255,255,255,0.5)' }}; font-family: var(--font-body1);">
                                        {{ $user->email_verified_at ? 'Verified' : 'Pending' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-3 sm:px-4 py-3 align-middle">
                                <div class="flex items-center justify-center gap-1 sm:gap-2">
                                    @if ($user->donor)
                                        <a href="/donors/{{ $user->donor->id }}" class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-white/10 hover:scale-110" title="View">
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
                                        <button type="button" class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-red-500/20 hover:scale-110" title="Delete" onclick="document.getElementById('deleteDonorUserModal_{{ $user->donor->id }}').showModal()">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                                            </svg>
                                        </button>

                                        <x-confirm-dialog id="deleteDonorUserModal_{{ $user->donor->id }}" title="Delete Donor" message="Are you sure you want to delete <strong>{{ $user->donor->display_name }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
                                            deleteUrl="/donors/{{ $user->donor->id }}" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <p class="text-sm" style="color: rgba(255,255,255,0.4); font-family: var(--font-body1);">No donor users found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if ($donorUsers->hasPages())
    <div class="flex justify-center pt-6 mt-4">
        {{ $donorUsers->links('pagination.custom') }}
    </div>
@endif