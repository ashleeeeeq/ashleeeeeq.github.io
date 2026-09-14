<x-dashboardlayout name="{{ $name }}" title="Activity Details">
    <div class="mb-8 space-y-6">
        <!-- Header Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-6 py-5">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-bold tracking-tight" style="font-family: var(--font-header1); color: var(--color-white);">{{ $activity->name }}</h1>
                            <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" 
                                style="background-color: {{ $activity->is_active ? 'rgba(16,185,129,0.15)' : 'rgba(255,255,255,0.05)' }}; color: {{ $activity->is_active ? 'var(--color-success)' : 'rgba(255,255,255,0.5)' }};">
                                {{ $activity->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <p class="mt-1 text-sm" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">{{ $activity->program?->program_name ?? 'N/A' }} ·
                            {{ $activity->activityType?->name ?? 'N/A' }} · {{ $activity->sportType?->name ?? '' }}
                        </p>
                        @if ($activity->description)
                            <div class="flex items-start gap-3 mt-4">
                                <div class="w-5 h-5 rounded-lg flex items-center justify-center shrink-0" style="background-color: rgba(255,204,51,0.15);">
                                    <svg class="w-3 h-3" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"></path>
                                    </svg>
                                </div>
                                <p class="text-sm leading-relaxed" style="color: rgba(255,255,255,0.8); font-family: var(--font-body1);">{{ $activity->description }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    @include('activities.partials.header-action-buttons')
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if (session('status'))
            <div class="p-4 rounded-xl flex items-center gap-3" style="background-color: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3);">
                <svg class="w-5 h-5 shrink-0" style="color: var(--color-success);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm" style="color: var(--color-success); font-family: var(--font-body1);">{{ session('status') }}</span>
            </div>
        @endif

        <!-- Tabs -->
        @include('activities.partials.tabs')

        <!-- Sessions Section -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08);">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Sessions</h2>
                            <p class="text-xs" style="color: rgba(255,255,255,0.6);">Manage activity sessions and attendance</p>
                        </div>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full" style="background-color: rgba(255,255,255,0.08); color: rgba(255,255,255,0.6);">{{ $sessions->total() }} sessions</span>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Schedule</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Created By</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Updated By</th>
                                <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Actions</th>
                             </tr>
                        </thead>
                        <tbody>
                            @forelse ($sessions as $index => $session)
                                <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}" style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                                    <td class="px-4 py-3 text-sm text-white" style="font-family: var(--font-body1);">{{ $session->schedule->format('M d, Y g:i A') }}</div>
                                    <td class="px-4 py-3 text-sm" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $session->creator->displayName }}</div>
                                    <td class="px-4 py-3 text-sm" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $session->updater->displayName }}</div>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2 flex-wrap">
                                            <a href="/activities/{{ $activity->id }}/sessions/{{ $session->id }}/attendance"
                                               class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 hover:scale-[1.02]"
                                               style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                                               onmouseover="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(0.95)'"
                                               onmouseout="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(1)'">
                                                Attendance
                                            </a>
                                            <a href="{{ route('activities.sessions.qr.print', [$activity, $session]) }}"
                                               target="_blank" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 hover:scale-[1.02]"
                                               style="font-family: var(--font-body1); background-color: transparent; color: var(--color-info); border: 1px solid var(--color-info);"
                                               onmouseover="this.style.backgroundColor='rgba(59,130,246,0.15)'; this.style.color='#60a5fa'; this.style.borderColor='#60a5fa'"
                                               onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-info)'; this.style.borderColor='var(--color-info)'">
                                                Show QR
                                            </a>
                                            <form method="POST" action="{{ route('activities.sessions.qr.regenerate', [$activity, $session]) }}" class="inline">
                                                @csrf
                                                <button type="button" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 hover:scale-[1.02]"
                                                        style="font-family: var(--font-body1); background-color: transparent; color: var(--color-warning); border: 1px solid var(--color-warning);"
                                                        onmouseover="this.style.backgroundColor='rgba(245,158,11,0.15)'; this.style.color='var(--color-accent1)'; this.style.borderColor='(--color-accent1)'"
                                                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-warning)'; this.style.borderColor='var(--color-warning)'"
                                                        onclick="document.getElementById('regenerateQrModal_{{ $session->id }}').showModal()">
                                                    Regenerate QR
                                                </button>

                                                <x-confirm-dialog id="regenerateQrModal_{{ $session->id }}" title="Regenerate Session QR" message="Regenerate this session QR? This will create a new code.">
                                                    <button type="submit" form="qrRegenerateForm_{{ $session->id }}" class="px-4 py-2 font-semibold rounded-lg transition-all duration-200" style="background-color: var(--color-warning); color: var(--color-primary1);">Regenerate</button>
                                                </x-confirm-dialog>
                                            </form>
                                            <form method="POST" action="{{ route('activities.sessions.qr.regenerate', [$activity, $session]) }}" id="qrRegenerateForm_{{ $session->id }}" class="hidden">
                                                @csrf
                                            </form>
                                            <a href="/activities/{{ $activity->id }}/sessions/{{ $session->id }}/edit"
                                               class="p-1.5 rounded-lg transition-all hover:bg-white/10" title="Edit">
                                                <svg class="w-4 h-4" style="color: rgba(255,255,255,0.6);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                                                </svg>
                                            </a>
                                            <button type="button" class="p-1.5 rounded-lg transition-all hover:bg-red-500/20" title="Delete" onclick="document.getElementById('deleteSessionModal_{{ $session->id }}').showModal()">
                                                <svg class="w-4 h-4" style="color: var(--color-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                                                </svg>
                                            </button>

                                            <x-confirm-dialog
                                                id="deleteSessionModal_{{ $session->id }}"
                                                title="Delete Session"
                                                message="Are you sure you want to delete this session? This action cannot be undone."
                                                deleteUrl="/activities/{{ $activity->id }}/sessions/{{ $session->id }}" />
                                        </div>
                                    </div>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-12 text-center">
                                        <svg class="w-12 h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-sm" style="color: rgba(255,255,255,0.4);">No sessions yet.</p>
                                    </div>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($sessions->hasPages())
                    <div class="flex justify-center pt-6 mt-4 border-t" style="border-color: rgba(255,255,255,0.08);">
                        {{ $sessions->links('pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Back Button -->
        <div class="flex justify-end">
            <a href="/activities" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold transition-all duration-300 hover:bg-white/10"
               style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);"
               onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
               onmouseout="this.style.backgroundColor='transparent'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
                Back to Activities
            </a>
        </div>
    </div>
</x-dashboardlayout>