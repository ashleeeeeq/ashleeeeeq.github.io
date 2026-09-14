<x-dashboardlayout name="{{ $name }}" title="{{ $title }}">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight" style="font-family: var(--font-header1); color: var(--color-white);">{{ $title }}</h1>
                <p class="text-sm mt-2" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">{{ $program?->program_name ?? 'All programs' }} · {{ $contextLabel }}</p>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">{{ $contextSubtitle }}</p>
                <div class="w-20 h-1 mt-3 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
        </div>

        @if ($errors->any())
            <div class="p-4 rounded-xl flex items-start gap-3" style="background-color: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.3);">
                <svg class="w-5 h-5 shrink-0 mt-0.5" style="color: var(--color-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                </svg>
                <div>
                    <span class="text-sm font-semibold" style="color: var(--color-danger);">Please fix the following error:</span>
                    <p class="text-sm mt-1" style="color: rgba(255,255,255,0.7);">{{ $errors->first() }}</p>
                </div>
            </div>
        @endif

        <!-- Filter Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4.5h18M3 9h18M3 13.5h18M3 18h18"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Filter Attendance List</h2>
                            <p class="text-xs" style="color: rgba(255,255,255,0.5);">Search by name or email, or filter by program and department</p>
                        </div>
                    </div>
                    <a href="{{ $filterUrl ?? url()->current() }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 hover:scale-[1.02]"
                       style="font-family: var(--font-body1); background-color: transparent; color: var(--color-info); border: 1px solid var(--color-info);"
                       onmouseover="this.style.backgroundColor='rgba(59,130,246,0.1)'; this.style.color='var(--color-info-dark)'"
                       onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-info)'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear filters
                    </a>
                </div>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ $filterUrl ?? url()->current() }}" class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-primary1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                    </svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}"
                                       placeholder="Search by name or email..."
                                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all hover:border-accent1 text-primary1"
                                       style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);">
                            </div>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Beneficiary Program</label>
                            <div class="relative">
                                <select name="beneficiary_program_id" class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none cursor-pointer hover:border-accent1"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;">
                                    <option value="" style="color: var(--color-secondary-dark1);">All programs</option>
                                    @foreach (($programOptions ?? []) as $programOption)
                                        <option value="{{ $programOption->id }}" @selected((string) ($selectedBeneficiaryProgramId ?? '') === (string) $programOption->id) style="color: var(--color-primary1);">{{ $programOption->program_name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Staff Department</label>
                            <div class="relative">
                                <select name="staff_department_id" class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none cursor-pointer hover:border-accent1"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;">
                                    <option value="" style="color: var(--color-secondary-dark1);">All departments</option>
                                    @foreach (($departmentOptions ?? []) as $departmentOption)
                                        <option value="{{ $departmentOption->id }}" @selected((string) ($selectedStaffDepartmentId ?? '') === (string) $departmentOption->id) style="color: var(--color-primary1);">{{ $departmentOption->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-5 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                                style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                                onmouseover="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(0.95)'"
                                onmouseout="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(1)'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4.5h18M3 9h18M3 13.5h18M3 18h18"></path>
                            </svg>
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <form method="POST" action="{{ $actionUrl }}" class="space-y-6">
            @csrf

            <!-- Beneficiaries Card -->
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center justify-between gap-4 flex-wrap">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                                <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Beneficiaries</h2>
                                <p class="text-xs" style="color: rgba(255,255,255,0.5);">{{ $beneficiaries->total() }} member/s</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.08); background-color: rgba(255,255,255,0.02);">
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Name</th>
                                    <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Status</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Remarks</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Updated By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($beneficiaries as $index => $beneficiary)
                                    @php
                                        $beneficiaryId = data_get($beneficiary, 'beneficiary_id', data_get($beneficiary, 'id'));
                                        $beneficiaryName = data_get($beneficiary, 'beneficiary.display_name', data_get($beneficiary, 'display_name'));
                                        $existing = $existingAttendances->get('beneficiary-'.$beneficiaryId);
                                    @endphp
                                    <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <td class="px-4 py-3 text-sm font-medium" style="color: var(--color-white); font-family: var(--font-body1);">{{ $beneficiaryName }}</div>
                                        <td class="px-4 py-3 text-center">
                                            <div class="relative">
                                                <select name="beneficiary_statuses[{{ $beneficiaryId }}]" onchange="styleAttendanceStatus(this)" data-attendance-status
                                                         class="w-full px-3 py-1.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none cursor-pointer hover:border-accent1 text-sm"
                                                         style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;">
                                                     <option value="" style="color: rgba(255,255,255,0.5);">Skip</option>
                                                     @foreach ($attendanceStatuses as $status)
                                                         @php
                                                             $statusColors = ['present' => '#10b981', 'absent' => '#ef4444', 'late' => '#eab308', 'excused' => '#f97316'];
                                                             $statusBg = ['present' => 'rgba(16,185,129,0.12)', 'absent' => 'rgba(239,68,68,0.12)', 'late' => 'rgba(234,179,8,0.12)', 'excused' => 'rgba(249,115,22,0.12)'];
                                                             $sc = $statusColors[$status] ?? 'var(--color-primary1)';
                                                         @endphp
                                                         <option value="{{ $status }}" @selected($existing?->attendance_status === $status) style="color: {{ $sc }};">{{ ucfirst($status) }}</option>
                                                     @endforeach
                                                 </select>
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                    <svg class="w-3 h-3" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        <td class="px-4 py-3">
                                            <input type="text" name="beneficiary_remarks[{{ $beneficiaryId }}]" 
                                                   class="w-full px-3 py-1.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1 text-sm"
                                                   style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                                                   value="{{ $existing?->remarks }}" placeholder="Optional note">
                                        </div>
                                        <td class="px-4 py-3 text-sm" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">{{ $existing?->updater?->display_name ?? '-' }}</div>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-12 text-center">
                                            <svg class="w-12 h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"></path>
                                            </svg>
                                            <p class="text-sm" style="color: rgba(255,255,255,0.4);">No beneficiaries in this program.</p>
                                        </div>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($beneficiaries->hasPages())
                        <div class="flex justify-center pt-6 mt-4 border-t" style="border-color: rgba(255,255,255,0.08);">
                            {{ $beneficiaries->links('pagination.custom') }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Staff Card -->
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center justify-between gap-4 flex-wrap">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                                <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Staff</h2>
                                <p class="text-xs" style="color: rgba(255,255,255,0.5);">{{ $staffMembers->total() }} member/s</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.08); background-color: rgba(255,255,255,0.02);">
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Name</th>
                                    <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Status</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Remarks</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Updated By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($staffMembers as $index => $staffMember)
                                    @php
                                        $staffId = data_get($staffMember, 'staff_id', data_get($staffMember, 'id'));
                                        $staffName = data_get($staffMember, 'staff.display_name', data_get($staffMember, 'display_name'));
                                        $existing = $existingAttendances->get('staff-'.$staffId);
                                    @endphp
                                    <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <td class="px-4 py-3 text-sm font-medium" style="color: var(--color-white); font-family: var(--font-body1);">{{ $staffName }}</div>
                                        <td class="px-4 py-3 text-center">
                                            <div class="relative">
                                                <select name="staff_statuses[{{ $staffId }}]" onchange="styleAttendanceStatus(this)" data-attendance-status
                                                         class="w-full px-3 py-1.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none cursor-pointer hover:border-accent1 text-sm"
                                                         style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;">
                                                     <option value="" style="color: rgba(255,255,255,0.5);">Skip</option>
                                                     @foreach ($attendanceStatuses as $status)
                                                         @php
                                                             $statusColors = ['present' => '#10b981', 'absent' => '#ef4444', 'late' => '#eab308', 'excused' => '#f97316'];
                                                             $sc = $statusColors[$status] ?? 'var(--color-primary1)';
                                                         @endphp
                                                         <option value="{{ $status }}" @selected($existing?->attendance_status === $status) style="color: {{ $sc }};">{{ ucfirst($status) }}</option>
                                                     @endforeach
                                                 </select>
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                    <svg class="w-3 h-3" style="color: rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        <td class="px-4 py-3">
                                            <input type="text" name="staff_remarks[{{ $staffId }}]" 
                                                   class="w-full px-3 py-1.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1 text-sm"
                                                   style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                                                   value="{{ $existing?->remarks }}" placeholder="Optional note">
                                        </div>
                                        <td class="px-4 py-3 text-sm" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">{{ $existing?->updater?->display_name ?? '-' }}</div>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-12 text-center">
                                            <svg class="w-12 h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"></path>
                                            </svg>
                                            <p class="text-sm" style="color: rgba(255,255,255,0.4);">No staff found.</p>
                                        </div>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($staffMembers->hasPages())
                        <div class="flex justify-center pt-6 mt-4 border-t" style="border-color: rgba(255,255,255,0.08);">
                            {{ $staffMembers->links('pagination.custom') }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4">
                <a href="{{ url()->previous() }}" 
                   class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] text-center inline-flex items-center gap-2 group justify-center"
                   style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                   onmouseover="this.style.backgroundColor='rgba(248,113,113,0.15)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)'"
                   onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-danger)'; this.style.borderColor='var(--color-danger)'">
                    <svg class="w-4 h-4 transition-all duration-300 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center gap-2 group justify-center"
                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                        onmouseover="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(0.95)'"
                        onmouseout="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(1)'">
                    <svg class="w-4 h-4 transition-all duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Save Attendance
                </button>
            </div>
        </form>
    </div>

    <script>
        function styleAttendanceStatus(el) {
            var val = el.value;
            var colors = { present: '#10b981', absent: '#ef4444', late: '#eab308', excused: '#f97316' };
            var bg = { present: 'rgba(16,185,129,0.15)', absent: 'rgba(239,68,68,0.15)', late: 'rgba(234,179,8,0.15)', excused: 'rgba(249,115,22,0.15)' };
            var isLight = document.documentElement.classList.contains('light-mode');
            var defaultColor = isLight ? '#1e293b' : 'white';
            var defaultBg = isLight ? '#ffffff' : 'rgba(255,255,255,0.05)';
            if (val && colors[val]) {
                el.style.color = colors[val];
                el.style.backgroundColor = bg[val];
            } else {
                el.style.color = defaultColor;
                el.style.backgroundColor = defaultBg;
            }
        }
        document.querySelectorAll('select[data-attendance-status]').forEach(function(el) {
            styleAttendanceStatus(el);
        });
    </script>

    <script>
        (function() {
            var pollUrl = '{{ $pollUrl ?? "" }}';
            if (!pollUrl) return;
            var lastPoll = new Date().toISOString();

            function poll() {
                fetch(pollUrl + '?since=' + encodeURIComponent(lastPoll))
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.attendances.length === 0) return;
                        data.attendances.forEach(function(att) {
                            var prefix = att.beneficiary_id ? 'beneficiary_statuses' : 'staff_statuses';
                            var id = att.beneficiary_id || att.staff_id;
                            var select = document.querySelector('select[name="' + prefix + '[' + id + ']"]');
                            if (!select) return;
                            if (select.value === '' || select.value !== att.attendance_status) {
                                select.value = att.attendance_status;
                                if (typeof styleAttendanceStatus === 'function') {
                                    styleAttendanceStatus(select);
                                }
                            }
                            var remarksInput = document.querySelector('input[name="' + (att.beneficiary_id ? 'beneficiary_remarks' : 'staff_remarks') + '[' + id + ']"]');
                            if (remarksInput && !remarksInput.value) {
                                remarksInput.value = att.remarks === '-' ? '' : att.remarks;
                            }
                            var row = select.closest('tr');
                            if (row) {
                                var cells = row.querySelectorAll('td');
                                var updaterCell = cells[cells.length - 1];
                                if (updaterCell) updaterCell.textContent = att.updated_by;
                            }
                        });
                        lastPoll = data.polled_at;
                    })
                    .catch(function() {});
            }

            setInterval(poll, 2000);
        })();
    </script>
</x-dashboardlayout>