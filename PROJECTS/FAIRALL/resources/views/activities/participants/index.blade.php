<x-dashboardlayout name="{{ $name }}" title="Activity Participants">
    <div class="mb-8 space-y-6 space-y-6">
        <!-- Header Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-6 py-5" style="border-color: rgba(255,255,255,0.08);">
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

        <div class="space-y-6 mt-4">
            @can('create-activities')
                <!-- Add Participants Section -->
                <div class="grid gap-6 lg:grid-cols-2">
                    <!-- Add Beneficiary Card -->
                    <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                        <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                            <div class="flex items-center justify-between gap-4 flex-wrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                                        <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Add Beneficiary</h2>
                                        <p class="text-xs" style="color: rgba(255,255,255,0.5);">Joined/exited history is preserved</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <form method="POST" action="/activities/{{ $activity->id }}/participants" class="space-y-4">
                                @csrf
                                <input type="hidden" name="participant_type" value="beneficiary">
                                <div class="relative">
                                    <select name="participant_id" data-choices="true" class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none cursor-pointer hover:border-accent1"
                                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;" required>
                                        <option value="" style="color: var(--color-secondary-dark1);">Select beneficiary</option>
                                        @foreach ($availableBeneficiaries as $beneficiary)
                                            <option value="{{ $beneficiary->id }}" style="color: var(--color-primary1);">{{ $beneficiary->display_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-4 h-4" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                        </svg>
                                    </div>
                                </div>
                                <button type="submit" 
                                        class="inline-flex items-center gap-2 px-5 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                                        onmouseover="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(0.95)'"
                                        onmouseout="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(1)'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                                    </svg>
                                    Add Beneficiary
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Add Staff Card -->
                    <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                        <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                            <div class="flex items-center justify-between gap-4 flex-wrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                                        <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Add Staff</h2>
                                        <p class="text-xs" style="color: rgba(255,255,255,0.5);">Program staff only</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <form method="POST" action="/activities/{{ $activity->id }}/participants" class="space-y-4">
                                @csrf
                                <input type="hidden" name="participant_type" value="staff">
                                <div class="relative">
                                    <select name="participant_id" data-choices="true" class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none cursor-pointer hover:border-accent1"
                                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;" required>
                                        <option value="" style="color: var(--color-secondary-dark1);">Select staff</option>
                                        @foreach ($availableStaff as $staffMember)
                                            <option value="{{ $staffMember->id }}" style="color: var(--color-primary1);">{{ $staffMember->display_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-4 h-4" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                        </svg>
                                    </div>
                                </div>
                                <button type="submit" 
                                        class="inline-flex items-center gap-2 px-5 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                                        onmouseover="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(0.95)'"
                                        onmouseout="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(1)'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                                    </svg>
                                    Add Staff
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan

            <!-- Active Participants Section -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Active Beneficiaries Card -->
                <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                    <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                        <div class="flex items-center justify-between gap-4 flex-wrap">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                                    <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Active Beneficiaries</h2>
                                    <p class="text-xs" style="color: rgba(255,255,255,0.5);">{{ $activeBeneficiaries->total() }} active participants</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @forelse ($activeBeneficiaries as $participant)
                                <div class="flex items-center justify-between rounded-xl border p-4 transition-all duration-200 hover:bg-white/5" style="border-color: rgba(255,255,255,0.08); background-color: rgba(255,255,255,0.02);">
                                    <div>
                                        <div class="font-semibold" style="font-family: var(--font-body1); color: var(--color-white);">{{ $participant->beneficiary?->display_name }}</div>
                                        <div class="text-xs mt-1" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Joined {{ $participant->joined_at?->format('M d, Y h:i A') }}</div>
                                    </div>
                                    @can('create-activities')
                                        <form method="POST" action="/activities/{{ $activity->id }}/participants/{{ $participant->id }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 hover:scale-[1.02]"
                                                    style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                                                    onmouseover="this.style.backgroundColor='rgba(248,113,113,0.15)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)'"
                                                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-danger)'; this.style.borderColor='var(--color-danger)'">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Exit
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"></path>
                                    </svg>
                                    <p class="text-sm" style="color: rgba(255,255,255,0.4);">No active beneficiaries.</p>
                                </div>
                            @endforelse
                        </div>
                        @if ($activeBeneficiaries->hasPages())
                            <div class="flex justify-center pt-6 mt-4 border-t" style="border-color: rgba(255,255,255,0.08);">
                                {{ $activeBeneficiaries->links('pagination.custom') }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Active Staff Card -->
                <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                    <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                        <div class="flex items-center justify-between gap-4 flex-wrap">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                                    <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Active Staff</h2>
                                    <p class="text-xs" style="color: rgba(255,255,255,0.5);">{{ $activeStaff->total() }} active staff</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @forelse ($activeStaff as $participant)
                                <div class="flex items-center justify-between rounded-xl border p-4 transition-all duration-200 hover:bg-white/5" style="border-color: rgba(255,255,255,0.08); background-color: rgba(255,255,255,0.02);">
                                    <div>
                                        <div class="font-semibold" style="font-family: var(--font-body1); color: var(--color-white);">{{ $participant->staff?->display_name }}</div>
                                        <div class="text-xs mt-1" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Joined {{ $participant->joined_at?->format('M d, Y h:i A') }}</div>
                                    </div>
                                    @can('create-activities')
                                        <form method="POST" action="/activities/{{ $activity->id }}/participants/{{ $participant->id }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 hover:scale-[1.02]"
                                                    style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                                                    onmouseover="this.style.backgroundColor='rgba(248,113,113,0.15)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)'"
                                                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-danger)'; this.style.borderColor='var(--color-danger)'">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Exit
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"></path>
                                    </svg>
                                    <p class="text-sm" style="color: rgba(255,255,255,0.4);">No active staff.</p>
                                </div>
                            @endforelse
                        </div>
                        @if ($activeStaff->hasPages())
                            <div class="flex justify-center pt-6 mt-4 border-t" style="border-color: rgba(255,255,255,0.08);">
                                {{ $activeStaff->links('pagination.custom') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboardlayout>