<x-dashboardlayout name="{{ $name }}" title="Edit Enrollment">
    <div class="mb-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Edit Enrollment</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Update enrollment details for {{ $beneficiary->display_name }}</p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
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

        <form method="post" action="/beneficiaries/{{ $beneficiary->id }}/enrollments/{{ $enrollment->id }}">
            @csrf
            @method('PUT')
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Enrollment Details</h3>
                            <p class="text-xs text-white/50">Update the school information and enrollment status.</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">School Name <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input name="school_name" value="{{ old('school_name', $enrollment->school_name) }}" class="w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all"
                                   style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);" required>
                            <x-forms.errors name="school_name" />
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Academic Year <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input name="academic_year" value="{{ old('academic_year', optional($enrollment->academic_year_start_date)->format('Y') . '-' . optional($enrollment->academic_year_end_date)->format('Y')) }}" class="w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all"
                                   style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);" placeholder="2025-2026" required>
                            <x-forms.errors name="academic_year" />
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Grade Level <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            @php
                                $gradeLevels = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];
                            @endphp
                            <div class="relative">
                                <select name="grade_level" class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all appearance-none"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;" required>
                                    @foreach ($gradeLevels as $gradeLevel)
                                        <option value="{{ $gradeLevel }}" style="color: var(--color-primary1);" @selected(old('grade_level', $enrollment->grade_level) === $gradeLevel)>{{ $gradeLevel }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                    </svg>
                                </div>
                            </div>
                            <x-forms.errors name="grade_level" />
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Enrollment Status</label>
                            @php
                                $enrollmentStatuses = ['active', 'completed', 'transferred', 'dropped', 'withdrawn', 'repeated'];
                            @endphp
                            <div class="relative">
                                <select name="enrollment_status" class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all appearance-none"
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;">
                                    @foreach ($enrollmentStatuses as $status)
                                        <option value="{{ $status }}" style="color: var(--color-primary1);" @selected(old('enrollment_status', $enrollment->enrollment_status) === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                    </svg>
                                </div>
                            </div>
                            <x-forms.errors name="enrollment_status" />
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 mt-4">
                        <a href="/beneficiaries/{{ $beneficiary->id }}/enrollments"
                           class="px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] text-center"
                           style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                                style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                            Update Enrollment
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-dashboardlayout>
