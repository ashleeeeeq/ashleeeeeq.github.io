<x-dashboardlayout name="{{ $name }}" title="Archive">
    <div class="mb-8 space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Archive</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Soft-deleted records across the system</p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
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

        <!-- Delete All Button -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3 archive-filter">
                <label class="text-sm font-medium text-white/70" style="font-family: var(--font-body1);">Filter by type:</label>
                <select data-choices="true" onchange="window.location.href = this.value">
                    <option value="/archive" {{ is_null($selectedType) ? 'selected' : '' }}>All Types ({{ $totalArchived }})</option>
                    @if(in_array('users_staff', $allowedTypes) || in_array('users_beneficiary', $allowedTypes) || in_array('users_donor', $allowedTypes))
                        <optgroup label="Users">
                            @if(in_array('users_staff', $allowedTypes))
                                <option value="/archive?type=users_staff" {{ $selectedType === 'users_staff' ? 'selected' : '' }}>Staff ({{ $counts['users_staff'] }})</option>
                            @endif
                            @if(in_array('users_beneficiary', $allowedTypes))
                                <option value="/archive?type=users_beneficiary" {{ $selectedType === 'users_beneficiary' ? 'selected' : '' }}>Beneficiaries ({{ $counts['users_beneficiary'] }})</option>
                            @endif
                            @if(in_array('users_donor', $allowedTypes))
                                <option value="/archive?type=users_donor" {{ $selectedType === 'users_donor' ? 'selected' : '' }}>Donors ({{ $counts['users_donor'] }})</option>
                            @endif
                        </optgroup>
                    @endif
                    @if(in_array('beneficiary_status_types', $allowedTypes) || in_array('assessment_categories', $allowedTypes) || in_array('sport_types', $allowedTypes) || in_array('activity_types', $allowedTypes) || in_array('event_types', $allowedTypes))
                        <optgroup label="Reference Data">
                            @if(in_array('beneficiary_status_types', $allowedTypes))
                                <option value="/archive?type=beneficiary_status_types" {{ $selectedType === 'beneficiary_status_types' ? 'selected' : '' }}>Beneficiary Status Types ({{ $counts['beneficiary_status_types'] }})</option>
                            @endif
                            @if(in_array('assessment_categories', $allowedTypes))
                                <option value="/archive?type=assessment_categories" {{ $selectedType === 'assessment_categories' ? 'selected' : '' }}>Assessment Categories ({{ $counts['assessment_categories'] }})</option>
                            @endif
                            @if(in_array('sport_types', $allowedTypes))
                                <option value="/archive?type=sport_types" {{ $selectedType === 'sport_types' ? 'selected' : '' }}>Sport Types ({{ $counts['sport_types'] }})</option>
                            @endif
                            @if(in_array('activity_types', $allowedTypes))
                                <option value="/archive?type=activity_types" {{ $selectedType === 'activity_types' ? 'selected' : '' }}>Activity Types ({{ $counts['activity_types'] }})</option>
                            @endif
                            @if(in_array('event_types', $allowedTypes))
                                <option value="/archive?type=event_types" {{ $selectedType === 'event_types' ? 'selected' : '' }}>Event Types ({{ $counts['event_types'] }})</option>
                            @endif
                        </optgroup>
                    @endif
                    @if(in_array('grants', $allowedTypes) || in_array('donations', $allowedTypes) || in_array('activities', $allowedTypes) || in_array('events', $allowedTypes) || in_array('competitions', $allowedTypes) || in_array('home_visits', $allowedTypes) || in_array('reports', $allowedTypes) || in_array('allocations', $allowedTypes) || in_array('funding_targets', $allowedTypes))
                        <optgroup label="Records">
                            @if(in_array('grants', $allowedTypes))
                                <option value="/archive?type=grants" {{ $selectedType === 'grants' ? 'selected' : '' }}>Grants ({{ $counts['grants'] }})</option>
                            @endif
                            @if(in_array('donations', $allowedTypes))
                                <option value="/archive?type=donations" {{ $selectedType === 'donations' ? 'selected' : '' }}>Donations ({{ $counts['donations'] }})</option>
                            @endif
                            @if(in_array('activities', $allowedTypes))
                                <option value="/archive?type=activities" {{ $selectedType === 'activities' ? 'selected' : '' }}>Activities ({{ $counts['activities'] }})</option>
                            @endif
                            @if(in_array('events', $allowedTypes))
                                <option value="/archive?type=events" {{ $selectedType === 'events' ? 'selected' : '' }}>Events ({{ $counts['events'] }})</option>
                            @endif
                            @if(in_array('competitions', $allowedTypes))
                                <option value="/archive?type=competitions" {{ $selectedType === 'competitions' ? 'selected' : '' }}>Competitions ({{ $counts['competitions'] }})</option>
                            @endif
                            @if(in_array('home_visits', $allowedTypes))
                                <option value="/archive?type=home_visits" {{ $selectedType === 'home_visits' ? 'selected' : '' }}>Home Visits ({{ $counts['home_visits'] }})</option>
                            @endif
                            @if(in_array('reports', $allowedTypes))
                                <option value="/archive?type=reports" {{ $selectedType === 'reports' ? 'selected' : '' }}>Reports ({{ $counts['reports'] }})</option>
                            @endif
                            @if(in_array('education_enrollments', $allowedTypes))
                                <option value="/archive?type=education_enrollments" {{ $selectedType === 'education_enrollments' ? 'selected' : '' }}>Education Enrollments ({{ $counts['education_enrollments'] }})</option>
                            @endif
                            @if(in_array('academic_records', $allowedTypes))
                                <option value="/archive?type=academic_records" {{ $selectedType === 'academic_records' ? 'selected' : '' }}>Academic Records ({{ $counts['academic_records'] }})</option>
                            @endif
                            @if(in_array('ffa_assessment_records', $allowedTypes))
                                <option value="/archive?type=ffa_assessment_records" {{ $selectedType === 'ffa_assessment_records' ? 'selected' : '' }}>FFA Assessment Records ({{ $counts['ffa_assessment_records'] }})</option>
                            @endif
                            @if(in_array('allocations', $allowedTypes))
                                <option value="/archive?type=allocations" {{ $selectedType === 'allocations' ? 'selected' : '' }}>Allocations ({{ $counts['allocations'] }})</option>
                            @endif
                            @if(in_array('funding_targets', $allowedTypes))
                                <option value="/archive?type=funding_targets" {{ $selectedType === 'funding_targets' ? 'selected' : '' }}>Funding Targets ({{ $counts['funding_targets'] }})</option>
                            @endif
                        </optgroup>
                    @endif
                </select>
            </div>
            @if ($totalArchived > 0)
                <div class="flex items-center gap-2">
                    <button type="button" onclick="document.getElementById('restoreAllArchivedModal').showModal()"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap"
                        style="background-color: #059669; color: white; font-family: var(--font-body1);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"></path>
                        </svg>
                        Restore All
                    </button>
                    <button type="button" onclick="document.getElementById('deleteAllArchivedModal').showModal()"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap"
                        style="background-color: var(--color-danger); color: white; font-family: var(--font-body1);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                        </svg>
                        Delete All
                    </button>
                </div>
            @endif
        </div>

        <!-- Archive Groups -->
        @forelse ($groups as $group)
            <div class="rounded-xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center justify-between gap-4 flex-wrap">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-semibold text-white text-sm sm:text-base" style="font-family: var(--font-header1);">{{ $group['label'] }}</h2>
                                <p class="text-xs" style="color: rgba(255,255,255,0.5);">{{ $group['items']->count() }} archived record(s)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="bulkBar-archive-{{ $group['key'] }}" data-bulk-bar="archive-{{ $group['key'] }}" class="hidden flex flex-wrap items-center gap-3 px-4 py-2.5 mx-6 mt-3 rounded-xl" style="background-color: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.10);">
                    <span class="text-sm flex items-center gap-1.5" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">
                        <span class="font-semibold" style="color: var(--color-accent1);" data-selected-count>0</span>
                        <span>selected</span>
                    </span>
                    <button type="button" data-bulk-restore-btn disabled class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-sm font-semibold transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed hover:scale-[1.02] hover:shadow-md" style="background-color: #059669; color: white; font-family: var(--font-body1);">Restore Selected</button>
                    <button type="button" data-bulk-delete-btn disabled class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-sm font-semibold transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed hover:scale-[1.02] hover:shadow-md" style="background-color: var(--color-danger); color: white; font-family: var(--font-body1);">Delete Permanently</button>
                    <button type="button" data-bulk-clear class="text-xs px-3 py-1.5 rounded-lg transition-colors hover:bg-white/10" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Clear</button>
                    <form id="restoreForm-{{ $group['key'] }}" method="POST" action="{{ route('archive.restore-selected') }}" class="hidden">@csrf<input type="hidden" name="type" value="{{ $group['key'] }}"><div data-restore-ids></div></form>
                    <form id="deleteForm-{{ $group['key'] }}" method="POST" action="{{ route('archive.force-delete-selected') }}" class="hidden">@csrf @method('DELETE')<input type="hidden" name="type" value="{{ $group['key'] }}"><div data-delete-ids></div></form>
                </div>

                <div class="overflow-x-auto">
                    <div class="p-6">
                        <table class="w-full" data-bulk-table="archive-{{ $group['key'] }}">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <th class="text-center px-3 sm:px-4 py-2 sm:py-3">
                                        <input type="checkbox" data-select-all class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                                    </th>
                                    <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Name</th>
                                    @if ($group['identifierField'])
                                        <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Login ID</th>
                                    @endif
                                    @if ($group['key'] === 'activity_types')
                                        <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Program</th>
                                    @endif
                                    @if ($group['key'] === 'home_visits')
                                        <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Beneficiary</th>
                                    @endif
                                    @if ($group['key'] === 'academic_records')
                                        <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Education Enrollment</th>
                                    @endif
                                    @if (in_array($group['key'], ['education_enrollments', 'academic_records', 'ffa_assessment_records']))
                                        <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Beneficiary</th>
                                    @endif
                                    @if ($group['key'] === 'allocations')
                                        <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Ref #</th>
                                        <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Amount</th>
                                    @endif
                                    @if ($group['key'] === 'funding_targets')
                                        <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Target Amount</th>
                                    @endif
                                    <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Deleted At</th>
                                    <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($group['items'] as $item)
                                    @php
                                        $nameField = $group['nameField'];
                                        $itemId = $item->id;
                                        $itemType = $group['key'];
                                    @endphp
                                    <tr class="transition-colors hover:bg-white/5 {{ $loop->index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                        <td class="px-3 sm:px-4 py-3 text-center">
                                            <input type="checkbox" value="{{ $itemId }}" data-row-checkbox class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                                        </td>
                                        <td class="px-3 sm:px-4 py-3">
                                            <span class="text-sm text-white" style="font-family: var(--font-body1);">{{ $item->$nameField }}</span>
                                        </td>
                                        @if ($group['identifierField'])
                                            <td class="px-3 sm:px-4 py-3">
                                                <span class="text-sm" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $item->login_id ?? 'N/A' }}</span>
                                            </td>
                                        @endif
                                        @if ($group['key'] === 'activity_types')
                                            <td class="px-3 sm:px-4 py-3">
                                                <span class="text-sm" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $item->extra_info ?? 'Unassigned' }}</span>
                                            </td>
                                        @endif
                                        @if ($group['key'] === 'home_visits')
                                            <td class="px-3 sm:px-4 py-3">
                                                <span class="text-sm" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $item->extra_info ?? 'No beneficiary' }}</span>
                                            </td>
                                        @endif
                                        @if ($group['key'] === 'academic_records')
                                            <td class="px-3 sm:px-4 py-3">
                                                <span class="text-sm" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $item->education_enrollment_info ?? 'No enrollment' }}</span>
                                            </td>
                                        @endif
                                        @if (in_array($group['key'], ['education_enrollments', 'academic_records', 'ffa_assessment_records']))
                                            <td class="px-3 sm:px-4 py-3">
                                                <span class="text-sm" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $item->extra_info ?? 'No beneficiary' }}</span>
                                            </td>
                                        @endif
                                        @if ($group['key'] === 'allocations')
                                            <td class="px-3 sm:px-4 py-3">
                                                <span class="text-sm" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $item->reference_info ?? '-' }}</span>
                                            </td>
                                            <td class="px-3 sm:px-4 py-3">
                                                <span class="text-sm" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $item->extra_info ?? '-' }}</span>
                                            </td>
                                        @endif
                                        @if ($group['key'] === 'funding_targets')
                                            <td class="px-3 sm:px-4 py-3">
                                                <span class="text-sm" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $item->extra_info ?? '-' }}</span>
                                            </td>
                                        @endif
                                        <td class="px-3 sm:px-4 py-3">
                                            <span class="text-sm" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">{{ $item->deleted_at->format('M d, Y h:i A') }}</span>
                                        </td>
                                        <td class="px-3 sm:px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button"
                                                    class="p-1.5 rounded-lg transition-all hover:bg-green-500/20 hover:scale-110"
                                                    title="Restore"
                                                    data-restore-type="{{ $itemType }}"
                                                    data-restore-id="{{ $itemId }}"
                                                    data-restore-name="{{ $item->$nameField }}"
                                                    onclick="openArchiveRestoreModal(this.dataset.restoreType, this.dataset.restoreId, this.dataset.restoreName)">
                                                    <svg class="w-4 h-4" style="color: #34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"></path>
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    class="p-1.5 rounded-lg transition-all hover:bg-red-500/20 hover:scale-110"
                                                    title="Permanently Delete"
                                                    onclick="document.getElementById('forceDeleteModal_{{ $itemType }}_{{ $itemId }}').showModal()">
                                                    <svg class="w-4 h-4" style="color: #f87171;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                                                    </svg>
                                                </button>

                                                <x-confirm-dialog id="forceDeleteModal_{{ $itemType }}_{{ $itemId }}"
                                                    title="Permanently Delete?"
                                                    message="This action will <strong>permanently delete</strong> this record. This cannot be undone.">
                                                    <form method="POST" action="{{ route('archive.force-destroy') }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="type" value="{{ $itemType }}">
                                                        <input type="hidden" name="id" value="{{ $itemId }}">
                                                        <button type="submit"
                                                            class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center justify-center gap-2"
                                                            style="font-family: var(--font-body1); background-color: var(--color-danger); color: white;"
                                                            onmouseover="this.style.backgroundColor='var(--color-danger-dark)'; this.style.filter='brightness(0.95)'"
                                                            onmouseout="this.style.backgroundColor='var(--color-danger)';">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                                                            </svg>
                                                            Delete Permanently
                                                        </button>
                                                    </form>
                                                </x-confirm-dialog>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <x-confirm-dialog id="bulkRestoreConfirm-{{ $group['key'] }}" title="Restore Selected" message="Are you sure you want to restore <span data-bulk-confirm-count class='font-bold'>0</span> records from <strong>{{ $group['label'] }}</strong>?">
                    <button type="button" id="bulkRestoreBtn-{{ $group['key'] }}" data-bulk-confirm-restore class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center justify-center gap-2" style="font-family: var(--font-body1); background-color: #059669; color: white;">Restore</button>
                </x-confirm-dialog>
                <x-confirm-dialog id="bulkDeleteConfirm-{{ $group['key'] }}" title="Permanently Delete Selected" message="Are you sure you want to <strong>permanently delete</strong> <span data-bulk-confirm-count class='font-bold'>0</span> records from <strong>{{ $group['label'] }}</strong>? This cannot be undone.">
                    <button type="button" id="bulkDeleteBtn-{{ $group['key'] }}" data-bulk-confirm-delete class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center justify-center gap-2" style="font-family: var(--font-body1); background-color: var(--color-danger); color: white;">Delete Permanently</button>
                </x-confirm-dialog>
            </div>
        @empty
            <div class="rounded-xl p-12 text-center" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <svg class="w-12 h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"></path>
                </svg>
                <p class="text-sm" style="color: rgba(255,255,255,0.4); font-family: var(--font-body1);">No archived records found{{ $selectedType ? ' for the selected type' : '' }}.</p>
            </div>
        @endforelse
    </div>

    <!-- Generic Single Restore Confirm Dialog -->
    <x-confirm-dialog id="archiveRestoreGenericModal" title="Restore Record?" message='Are you sure you want to restore <strong id="archiveRestoreGenericName"></strong>?'>
        <form id="archiveRestoreGenericForm" method="POST" action="{{ route('archive.restore') }}" class="inline">
            @csrf
            <input type="hidden" name="type" id="archiveRestoreGenericType" value="">
            <input type="hidden" name="id" id="archiveRestoreGenericId" value="">
            <button type="submit"
                class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center justify-center gap-2"
                style="font-family: var(--font-body1); background-color: #059669; color: white;"
                onmouseover="this.style.backgroundColor='#047857'; this.style.filter='brightness(0.95)'"
                onmouseout="this.style.backgroundColor='#059669';">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"></path>
                </svg>
                Restore
            </button>
        </form>
    </x-confirm-dialog>
    <script>
        function openArchiveRestoreModal(type, id, name) {
            document.getElementById('archiveRestoreGenericType').value = type;
            document.getElementById('archiveRestoreGenericId').value = id;
            document.getElementById('archiveRestoreGenericName').textContent = name;
            document.getElementById('archiveRestoreGenericModal').showModal();
        }
    </script>

    <!-- Restore All Modal -->
    <x-confirm-dialog id="restoreAllArchivedModal" title="Restore All?" message="This will restore all archived records shown on this page.">
        <form method="POST" action="{{ route('archive.restore-all') }}{{ $selectedType ? '?type=' . $selectedType : '' }}" class="inline">
            @csrf
            <button type="submit"
                class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center justify-center gap-2"
                style="font-family: var(--font-body1); background-color: #059669; color: white;"
                onmouseover="this.style.backgroundColor='#047857'; this.style.filter='brightness(0.95)'"
                onmouseout="this.style.backgroundColor='#059669';">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"></path>
                </svg>
                Restore All
            </button>
        </form>
    </x-confirm-dialog>

    <!-- Delete All Warning Modal -->
    <x-confirm-dialog
        id="deleteAllArchivedModal"
        title="Permanently Delete All?"
        message="This action will <strong>permanently delete</strong> all archived records shown on this page. This cannot be undone."
        deleteButtonText="Delete All Permanently"
        deleteUrl="{{ route('archive.force-delete-all') }}{{ $selectedType ? '?type=' . $selectedType : '' }}" />
</x-dashboardlayout>
