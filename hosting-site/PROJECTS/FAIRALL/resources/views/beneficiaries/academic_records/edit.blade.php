<x-dashboardlayout name="{{ $name }}" title="Edit Academic Record">
    @php
        $enrollment = $record->educationEnrollment;
    @endphp
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Edit
                    Academic Record</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Update academic performance
                    record</p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        <!-- Beneficiary Info Alert -->
        <div class="mb-6 p-4 rounded-xl flex items-center gap-3"
            style="background-color: rgba(255,204,51,0.1); border: 1px solid rgba(255,204,51,0.3);">
            <svg class="w-5 h-5" style="color: var(--color-accent1);" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="text-sm" style="color: var(--color-accent1); font-family: var(--font-body1);">
                Beneficiary: <strong>{{ $beneficiary->display_name }}</strong>
            </span>
        </div>

        <form method="POST" action="/beneficiaries/{{ $beneficiary->id }}/academic-records/{{ $record->id }}">
            @csrf
            @method('PUT')

            <!-- Enrollment Details -->
            <div class="rounded-xl overflow-hidden mb-6" style="border: 1px solid rgba(255,255,255,0.1);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
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
                            <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Enrollment
                                Details</h3>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="rounded-lg p-4"
                            style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                            <p class="text-xs uppercase tracking-wider mb-2" style="color: rgba(255,255,255,0.5);">
                                School Name</p>
                            <p class="text-white font-medium">
                                {{ $enrollment?->school_name ?? $record->school_name }}
                            </p>
                        </div>
                        <div class="rounded-lg p-4"
                            style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                            <p class="text-xs uppercase tracking-wider mb-2" style="color: rgba(255,255,255,0.5);">
                                Academic Year</p>
                            <p class="text-white font-medium">
                                {{ $enrollment ? $enrollment->academic_year_start_date->format('Y') . '-' . $enrollment->academic_year_end_date->format('Y') : $record->academic_year }}
                            </p>
                        </div>
                        <div class="rounded-lg p-4"
                            style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                            <p class="text-xs uppercase tracking-wider mb-2" style="color: rgba(255,255,255,0.5);">Grade
                                Level</p>
                            <p class="text-white font-medium">
                                {{ $enrollment?->grade_level ?? $record->grade_level }}
                            </p>
                        </div>
                    </div>

                    <input type="hidden" name="enrollment_id" value="{{ $record->education_enrollment_id }}">
                </div>
            </div>

            <!-- Academic Details -->
            <div class="rounded-xl overflow-hidden mb-6" style="border: 1px solid rgba(255,255,255,0.1);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
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
                            <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Academic
                                Details</h3>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Term <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <div class="relative">
                                <select name="term"
                                    class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all appearance-none"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;"
                                    required>
                                    <option value="" style="color: var(--color-primary1);" disabled>Select a term
                                    </option>
                                    <option value="1st" style="color: var(--color-primary1);"
                                        @if ($record->term === '1st') selected @endif>1st</option>
                                    <option value="2nd" style="color: var(--color-primary1);"
                                        @if ($record->term === '2nd') selected @endif>2nd</option>
                                    <option value="3rd" style="color: var(--color-primary1);"
                                        @if ($record->term === '3rd') selected @endif>3rd</option>
                                    <option value="4th" style="color: var(--color-primary1);"
                                        @if ($record->term === '4th') selected @endif>4th</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                    </svg>
                                </div>
                            </div>
                            <x-forms.errors name="term" />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Attendance (Percent-based) <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="number" step="0.01" min="0" max="100"
                                name="school_attendance" value="{{ old('school_attendance', $record->school_attendance) }}"
                                class="w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                                placeholder="Enter attendance" required>
                            <x-forms.errors name="school_attendance" />
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">GWA
                                (Percent-based)
                            </label>
                            <div class="flex items-center gap-4 mb-3">
                                <span class="text-xs" style="color: rgba(255,255,255,0.5);">Auto-calculate GWA?</span>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="auto_calculate_gwa" value="1"
                                        class="w-3.5 h-3.5" style="accent-color: var(--color-accent1);"
                                        @checked(!old('auto_calculate_gwa', true))>
                                    <span class="text-xs" style="color: rgba(255,255,255,0.7);">Yes</span>
                                </label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="auto_calculate_gwa" value="0"
                                        class="w-3.5 h-3.5" style="accent-color: var(--color-accent1);"
                                        @checked(old('auto_calculate_gwa') === '0')>
                                    <span class="text-xs" style="color: rgba(255,255,255,0.7);">No</span>
                                </label>
                            </div>
                            <input type="number" step="0.01" min="0" max="100" name="manual_gwa"
                                value="{{ old('manual_gwa', $record->gwa) }}"
                                class="w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                                placeholder="Enter GWA if no subject grades are provided">
                            <x-forms.errors name="manual_gwa" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subject Grades Section -->
            <div class="rounded-xl overflow-hidden mt-6" style="border: 1px solid rgba(255,255,255,0.1);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center justify-between gap-3 flex-wrap">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                style="background-color: rgba(255,204,51,0.15);">
                                <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Subject
                                    Grades</h3>
                                <p class="text-xs text-white/50">Update the grades below, or clear them and use the GWA
                                    field above.</p>
                            </div>
                        </div>
                        <button type="button" id="add-subject-grade"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02]"
                            style="font-family: var(--font-body1); background-color: transparent; color: var(--color-accent1); border: 1px solid var(--color-accent1);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Subject
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <x-forms.errors name="subject_grades" />
                    <div id="subject-grade-rows" class="space-y-3"></div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 pt-6 mt-4">
                <a href="/beneficiaries/{{ $beneficiary->id }}/academic-records"
                    class="px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] text-center"
                    style="
                        font-family: var(--font-body1); 
                        background-color: transparent; 
                        color: var(--color-danger); 
                        border: 1px solid var(--color-danger);
                    "
                    onmouseover="
                        this.style.backgroundColor='var(--color-danger-light)'; 
                        this.style.color='var(--color-danger-dark)'; 
                        this.style.borderColor='var(--color-danger-dark)';
                    "
                    onmouseout="
                        this.style.backgroundColor='transparent'; 
                        this.style.color='var(--color-danger)'; 
                        this.style.borderColor='var(--color-danger)';
                    ">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                    style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                    Update Record
                </button>
            </div>
        </form>
    </div>

    <template id="subject-grade-template">
        <div class="grid gap-3 rounded-lg p-4 subject-grade-row"
            style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
            <div class="grid grid-cols-1 md:grid-cols-[1fr_160px_auto] gap-3">
                <div>
                    <label class="block mb-1 text-xs font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.6);">Subject Name <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                    <input name="subject_grades[__INDEX__][subject_name]"
                        class="w-full px-3 py-2 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all text-sm"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                        required>
                </div>
                <div>
                    <label class="block mb-1 text-xs font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.6);">Grade <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                    <input type="number" step="0.01" min="0" max="100"
                        name="subject_grades[__INDEX__][grade]"
                        class="w-full px-3 py-2 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all text-sm"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                        required>
                </div>
                <div class="flex items-end">
                    <button type="button"
                        class="remove-subject-grade inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg transition-all duration-200 bg-danger/30"
                        style="font-family: var(--font-body1); color: var(--color-danger);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Remove
                    </button>
                </div>
            </div>
        </div>
    </template>

    <script>
        (() => {
            const existingGrades = @json(old(
                    'subject_grades',
                    $record->subjectGrades->map(fn($grade) => ['subject_name' => $grade->subject_name, 'grade' => $grade->grade])->values()));
            const rows = document.getElementById('subject-grade-rows');
            const template = document.getElementById('subject-grade-template').innerHTML;
            const addButton = document.getElementById('add-subject-grade');
            const reindexRows = () => {
                rows.querySelectorAll('.subject-grade-row').forEach((row, index) => {
                    row.querySelectorAll('input').forEach((input) => {
                        input.name = input.name.replace(/subject_grades\[\d+\]/,
                            `subject_grades[${index}]`);
                    });
                });
            };

            const addRow = (subjectName = '', grade = '') => {
                const index = rows.querySelectorAll('.subject-grade-row').length;
                rows.insertAdjacentHTML('beforeend', template.replaceAll('__INDEX__', index));
                const row = rows.lastElementChild;
                row.querySelector('input[name$="[subject_name]"]').value = subjectName;
                row.querySelector('input[name$="[grade]"]').value = grade;
                row.querySelector('.remove-subject-grade').addEventListener('click', () => {
                    row.remove();
                    reindexRows();
                });
            };

            addButton.addEventListener('click', () => addRow());
            existingGrades.length ? existingGrades.forEach((grade) => addRow(grade.subject_name, grade.grade)) : null;
        })();
    </script>
</x-dashboardlayout>
