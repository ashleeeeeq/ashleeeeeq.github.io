<x-dashboardlayout name="{{ $name }}" title="Enrollment Details">
    <div class="mb-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Enrollment Details</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">View school enrollment and linked academic records</p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="/beneficiaries/{{ $beneficiary->id }}/academic-records/create?enrollment_id={{ $enrollment->id }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap"
                   style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                    </svg>
                    Add Academic Record
                </a>
                <a href="/beneficiaries/{{ $beneficiary->id }}/enrollments/{{ $enrollment->id }}/edit"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap"
                   style="font-family: var(--font-body1); background-color: transparent; color: var(--color-info); border: 1px solid var(--color-info);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                    </svg>
                    Edit Enrollment
                </a>
            </div>
        </div>

        <div class="mb-6 p-4 rounded-xl flex items-center gap-3" style="background-color: rgba(255,204,51,0.1); border: 1px solid rgba(255,204,51,0.3);">
            <svg class="w-5 h-5" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="text-sm" style="color: var(--color-accent1); font-family: var(--font-body1);">
                Beneficiary: <strong>{{ $beneficiary->display_name }}</strong>
            </span>
        </div>

        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                        <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">{{ $enrollment->school_name }}</h2>
                        <p class="text-xs text-white/50">{{ $enrollment->academic_year_start_date->format('Y') }} - {{ $enrollment->academic_year_end_date->format('Y') }} • {{ $enrollment->grade_level }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid gap-4 md:grid-cols-4 mb-6">
                    <div class="rounded-lg p-4" style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <p class="text-xs uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">School</p>
                        </div>
                        <p class="text-white font-medium">{{ $enrollment->school_name }}</p>
                    </div>
                    <div class="rounded-lg p-4" style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"></path>
                            </svg>
                            <p class="text-xs uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Academic Year</p>
                        </div>
                        <p class="text-white font-medium">{{ $enrollment->academic_year_start_date->format('Y') }} - {{ $enrollment->academic_year_end_date->format('Y') }}</p>
                    </div>
                    <div class="rounded-lg p-4" style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-xs uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Grade Level</p>
                        </div>
                        <p class="text-white font-medium">{{ $enrollment->grade_level }}</p>
                    </div>
                    <div class="rounded-lg p-4" style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4" style="color: var(--color-success);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-xs uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Status</p>
                        </div>
                        <p class="text-white font-medium">{{ $enrollment->enrollment_status }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Academic Records</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.6);">Academic Year</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.6);">Term</th>
                                    <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.6);">GWA</th>
                                    <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.6);">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($academicRecords as $record)
                                    <tr class="hover:bg-white/5 transition-colors" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <td class="px-4 py-3 text-sm text-white">{{ $record->academic_year }}</td>
                                        <td class="px-4 py-3 text-sm" style="color: rgba(255,255,255,0.8);">{{ $record->term }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full" style="background-color: rgba(255,204,51,0.15); color: var(--color-accent1);">
                                                {{ number_format($record->gwa, 2) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="/beneficiaries/{{ $beneficiary->id }}/academic-records/{{ $record->id }}" class="text-sm" style="color: var(--color-info);">View</a>
                                            <a href="/beneficiaries/{{ $beneficiary->id }}/academic-records/{{ $record->id }}/edit{{ $record->education_enrollment_id ? '?enrollment_id=' . $record->education_enrollment_id : '' }}" class="text-sm ml-3" style="color: var(--color-warning);">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-12 text-center">
                                            <svg class="w-12 h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.3);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253"></path>
                                            </svg>
                                            <p class="text-sm" style="color: rgba(255,255,255,0.4);">No academic records available for this enrollment.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboardlayout>
