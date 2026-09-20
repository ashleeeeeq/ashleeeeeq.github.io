<x-dashboardlayout name="{{ $name }}" title="Activity History">
    <div class="mb-8 space-y-6 space-y-6">
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
                                <div class="w-5 h-5 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: rgba(255,204,51,0.15);">
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
                <svg class="w-5 h-5 flex-shrink-0" style="color: var(--color-success);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm" style="color: var(--color-success); font-family: var(--font-body1);">{{ session('status') }}</span>
            </div>
        @endif

        <!-- Tabs -->
        @include('activities.partials.tabs')

        <!-- Participation History Card -->
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
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Participation History</h2>
                            <p class="text-xs" style="color: rgba(255,255,255,0.6);">Joined and exit dates stay recorded</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Type</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Name</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Joined</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Exited</th>
                                <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($participantRecords as $index => $participant)
                                <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                    <td class="px-4 py-3 text-sm" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">
                                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" 
                                              style="background-color: {{ $participant->beneficiary_id ? 'rgba(59,130,246,0.15)' : 'rgba(255,204,51,0.15)' }}; color: {{ $participant->beneficiary_id ? 'var(--color-info)' : 'var(--color-accent1)' }};">
                                            {{ $participant->beneficiary_id ? 'Beneficiary' : 'Staff' }}
                                        </span>
                                    </div>
                                    <td class="px-4 py-3 text-sm font-medium" style="color: var(--color-white); font-family: var(--font-body1);">
                                        {{ $participant->beneficiary?->display_name ?? $participant->staff?->display_name }}
                                    </div>
                                    <td class="px-4 py-3 text-sm" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">
                                        {{ $participant->joined_at?->format('M d, Y h:i A') }}
                                    </div>
                                    <td class="px-4 py-3 text-sm" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">
                                        {{ $participant->exited_at?->format('M d, Y h:i A') ?? 'Active' }}
                                    </div>
                                    <td class="px-4 py-3 text-center">
                                        @if ($participant->exited_at)
                                            <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" style="background-color: rgba(248,113,113,0.15); color: var(--color-danger);">
                                                Exited
                                            </span>
                                        @else
                                            <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" style="background-color: rgba(16,185,129,0.15); color: var(--color-success);">
                                                Active
                                            </span>
                                        @endif
                                    </div>
                                <tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-12 text-center">
                                        <svg class="w-12 h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-sm" style="color: rgba(255,255,255,0.4);">No participation history yet.</p>
                                    </div>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($participantRecords->hasPages())
                    <div class="flex justify-center pt-6 mt-4 border-t" style="border-color: rgba(255,255,255,0.08);">
                        {{ $participantRecords->links('pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-dashboardlayout>