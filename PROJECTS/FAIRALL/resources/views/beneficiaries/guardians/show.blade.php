<x-dashboardlayout name="{{ $name }}" title="Guardian Details">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Guardian Details</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Beneficiary: <span class="font-semibold text-white">{{ $beneficiary->display_name }}</span></p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        <!-- Guardian Details Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">{{ ucfirst($guardian->guardian_type) }}</h2>
                            <p class="text-xs text-white/50">Guardian Information</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="/beneficiaries/{{ $beneficiary->id }}/guardians/{{ $guardian->id }}/edit" 
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold transition-all duration-200 hover:scale-[1.02]"
                           style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                            </svg>
                            Edit
                        </a>
                        <button onclick="document.getElementById('deleteModal').showModal()" 
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold transition-all duration-200 hover:scale-[1.02]"
                                style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                            </svg>
                            Delete
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    <!-- Personal Information Section -->
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-accent1); font-family: var(--font-body1);">Personal Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <div class="text-xs font-medium uppercase tracking-wide" style="color: rgba(255,255,255,0.5);">First Name</div>
                                <div class="text-base font-semibold text-white" style="font-family: var(--font-body1);">{{ $guardian->first_name }}</div>
                            </div>
                            <div class="space-y-1">
                                <div class="text-xs font-medium uppercase tracking-wide" style="color: rgba(255,255,255,0.5);">Middle Name</div>
                                <div class="text-base font-semibold text-white" style="font-family: var(--font-body1);">{{ $guardian->middle_name ?? 'N/A' }}</div>
                            </div>
                            <div class="space-y-1">
                                <div class="text-xs font-medium uppercase tracking-wide" style="color: rgba(255,255,255,0.5);">Last Name</div>
                                <div class="text-base font-semibold text-white" style="font-family: var(--font-body1);">{{ $guardian->last_name }}</div>
                            </div>
                            <div class="space-y-1">
                                <div class="text-xs font-medium uppercase tracking-wide" style="color: rgba(255,255,255,0.5);">Sex</div>
                                <div class="text-base font-semibold text-white" style="font-family: var(--font-body1);">{{ $guardian->sex ? ucfirst($guardian->sex) : 'N/A' }}</div>
                            </div>
                            <div class="space-y-1">
                                <div class="text-xs font-medium uppercase tracking-wide" style="color: rgba(255,255,255,0.5);">Birth Date</div>
                                <div class="text-base font-semibold text-white" style="font-family: var(--font-body1);">{{ $guardian->birth_date ? \Carbon\Carbon::parse($guardian->birth_date)->format('F d, Y') : 'N/A' }}</div>
                            </div>
                            <div class="space-y-1">
                                <div class="text-xs font-medium uppercase tracking-wide" style="color: rgba(255,255,255,0.5);">Place of Birth</div>
                                <div class="text-base font-semibold text-white" style="font-family: var(--font-body1);">{{ $guardian->place_of_birth ?? 'N/A' }}</div>
                            </div>
                            <div class="space-y-1">
                                <div class="text-xs font-medium uppercase tracking-wide" style="color: rgba(255,255,255,0.5);">Civil Status</div>
                                <div class="text-base font-semibold text-white" style="font-family: var(--font-body1);">{{ $guardian->civil_status ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <div style="height: 1px; background-color: rgba(255,255,255,0.1);"></div>

                    <!-- Contact & Employment Section -->
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-accent1); font-family: var(--font-body1);">Contact & Employment</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <div class="text-xs font-medium uppercase tracking-wide" style="color: rgba(255,255,255,0.5);">Contact Number</div>
                                <div class="text-base font-semibold text-white" style="font-family: var(--font-body1);">{{ $guardian->formatted_contact }}</div>
                            </div>
                            <div class="space-y-1">
                                <div class="text-xs font-medium uppercase tracking-wide" style="color: rgba(255,255,255,0.5);">Occupation</div>
                                <div class="text-base font-semibold text-white" style="font-family: var(--font-body1);">{{ $guardian->job ?? 'N/A' }}</div>
                            </div>
                            <div class="space-y-1">
                                <div class="text-xs font-medium uppercase tracking-wide" style="color: rgba(255,255,255,0.5);">Highest Education</div>
                                <div class="text-base font-semibold text-white" style="font-family: var(--font-body1);">{{ $guardian->highest_education ? ucwords(str_replace('_', ' ', $guardian->highest_education)) : 'N/A' }}</div>
                            </div>
                            <div class="space-y-1">
                                <div class="text-xs font-medium uppercase tracking-wide" style="color: rgba(255,255,255,0.5);">Estimated Salary</div>
                                <div class="text-base font-semibold text-white" style="font-family: var(--font-body1);">{{ $guardian->estimated_salary ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <div style="height: 1px; background-color: rgba(255,255,255,0.1);"></div>

                    <!-- Address Section -->
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-accent1); font-family: var(--font-body1);">Address</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <div class="text-xs font-medium uppercase tracking-wide" style="color: rgba(255,255,255,0.5);">Address Line</div>
                                <div class="text-base font-semibold text-white" style="font-family: var(--font-body1);">{{ $guardian->address?->address_line ?? 'N/A' }}</div>
                            </div>
                            <div class="space-y-1">
                                <div class="text-xs font-medium uppercase tracking-wide" style="color: rgba(255,255,255,0.5);">City</div>
                                <div class="text-base font-semibold text-white" style="font-family: var(--font-body1);">{{ $guardian->address?->city ?? 'N/A' }}</div>
                            </div>
                            <div class="space-y-1">
                                <div class="text-xs font-medium uppercase tracking-wide" style="color: rgba(255,255,255,0.5);">State / Province</div>
                                <div class="text-base font-semibold text-white" style="font-family: var(--font-body1);">{{ $guardian->address?->province ?? 'N/A' }}</div>
                            </div>
                            <div class="space-y-1">
                                <div class="text-xs font-medium uppercase tracking-wide" style="color: rgba(255,255,255,0.5);">Country</div>
                                <div class="text-base font-semibold text-white" style="font-family: var(--font-body1);">{{ $guardian->address?->country ?? 'N/A' }}</div>
                            </div>
                            <div class="space-y-1">
                                <div class="text-xs font-medium uppercase tracking-wide" style="color: rgba(255,255,255,0.5);">ZIP Code</div>
                                <div class="text-base font-semibold text-white" style="font-family: var(--font-body1);">{{ $guardian->address?->zip ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <div style="height: 1px; background-color: rgba(255,255,255,0.1);"></div>

                    <!-- Status Section -->
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wider mb-4" style="color: var(--color-accent1); font-family: var(--font-body1);">Status</h3>
                        <div>
                            @if ($guardian->deceased)
                                <span class="inline-flex px-3 py-1.5 text-sm font-medium rounded-full" style="background-color: rgba(248,113,113,0.15); color: var(--color-danger);">
                                    Deceased
                                </span>
                            @else
                                <span class="inline-flex px-3 py-1.5 text-sm font-medium rounded-full" style="background-color: rgba(16,185,129,0.15); color: var(--color-success);">
                                    Living
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="mt-8 pt-6 border-t" style="border-color: rgba(255,255,255,0.1);">
                    <a href="/beneficiaries/{{ $beneficiary->id }}/guardians" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold transition-all duration-300 hover:bg-white/10"
                       style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                        </svg>
                        Back to Guardians
                    </a>
                </div>
            </div>
        </div>
    </div>

    <x-confirm-dialog id="deleteModal" title="Delete Guardian"
        message="Are you sure you want to delete <strong>{{ $guardian->first_name }} {{ $guardian->last_name }}</strong>? This action cannot be undone."
        deleteUrl="/beneficiaries/{{ $beneficiary->id }}/guardians/{{ $guardian->id }}" />
</x-dashboardlayout>