<x-dashboardlayout name="{{ $name }}" title="Enrollments">
    <div class="mb-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Education
                    Enrollments</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Manage school enrollments
                    for {{ $beneficiary->display_name }}</p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        @include('beneficiaries.partials.profile-tabs')
        @include('beneficiaries.partials.academic-subtabs')

        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Enrollment
                                History</h2>
                            <p class="text-xs text-white/50">All school enrollments associated with this beneficiary</p>
                        </div>
                    </div>
                    <a href="/beneficiaries/{{ $beneficiary->id }}/enrollments/create"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap"
                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.5v15m7.5-7.5h-15">
                            </path>
                        </svg>
                        New Enrollment
                    </a>
                </div>
            </div>
            <div class="px-4 sm:px-6 pb-3">
                <x-bulk-actions-bar bulkUrl="{{ route('beneficiaries.enrollments.bulk-destroy', $beneficiary) }}" tableId="enrollments-{{ $beneficiary->id }}" label="enrollments" />
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-225" data-bulk-table="enrollments-{{ $beneficiary->id }}">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                <th class="text-center px-3 sm:px-4 py-2 sm:py-3">
                                    <input type="checkbox" data-select-all class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                                </th>
                                <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">School</th>
                                <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Academic Year
                                </th>
                                <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Grade Level
                                </th>
                                <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Status</th>
                                <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enrollments as $index => $enrollment)
                                <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}"
                                    style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                    <td class="px-3 sm:px-4 py-3 align-middle text-center">
                                        <input type="checkbox" value="{{ $enrollment->id }}" data-row-checkbox class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm text-white">{{ $enrollment->school_name }}
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 align-middle text-center text-sm"
                                        style="color: rgba(255,255,255,0.8);">
                                        {{ $enrollment->academic_year_start_date->format('Y') }} -
                                        {{ $enrollment->academic_year_end_date->format('Y') }}</td>
                                    <td class="px-3 sm:px-4 py-3 align-middle text-center text-sm"
                                        style="color: rgba(255,255,255,0.8);">
                                        {{ $enrollment->grade_level }}</td>
                                    <td class="px-3 sm:px-4 py-3 align-middle text-center text-sm"
                                        style="color: rgba(255,255,255,0.8);">
                                        {{ $enrollment->enrollment_status }}</td>
                                    <td class="px-3 sm:px-4 py-3 align-middle text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="/beneficiaries/{{ $beneficiary->id }}/enrollments/{{ $enrollment->id }}"
                                                class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-white/10 hover:scale-110"
                                                title="View">
                                                <svg class="w-4 h-4"
                                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </a>
                                            <a href="/beneficiaries/{{ $beneficiary->id }}/enrollments/{{ $enrollment->id }}/edit"
                                                class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-white/10 hover:scale-110"
                                                title="Edit">
                                                <svg class="w-4 h-4"
                                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10">
                                                    </path>
                                                </svg>
                                            </a>
                                            <button type="button"
                                                class="p-1.5 rounded-lg transition-all hover:bg-red-500/20"
                                                title="Delete"
                                                onclick="document.getElementById('deleteModal_{{ $enrollment->id }}').showModal()">
                                                <svg class="w-4 h-4" style="color: var(--color-danger);" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                                                    </path>
                                                </svg>
                                            </button>

                                            <x-confirm-dialog id="deleteModal_{{ $enrollment->id }}"
                                                title="Delete Education Enrollment"
                                                message="Are you sure you want to delete the enrollment record for <strong>{{ $enrollment->school_name }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
                                                deleteUrl="/beneficiaries/{{ $beneficiary->id }}/enrollments/{{ $enrollment->id }}" />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center">
                                        <svg class="w-12 h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.3);"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                            </path>
                                        </svg>
                                        <p class="text-sm" style="color: rgba(255,255,255,0.4);">No enrollments found.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if (method_exists($enrollments, 'hasPages') && $enrollments->hasPages())
                <div class="flex justify-center pb-6">
                    {{ $enrollments->links('pagination.custom') }}
                </div>
            @endif
        </div>
    </div>
</x-dashboardlayout>
