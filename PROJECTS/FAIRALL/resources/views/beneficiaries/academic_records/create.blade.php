<x-dashboardlayout name="{{ $name }}" title="Create Academic Record">
    @php
        $enrollmentsList = $enrollments ?? collect();
        $defaultEnrollmentId = $selectedEnrollment?->id;
    @endphp
    <div class="mb-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Create
                    Academic Record</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Add academic performance
                    record(s) for beneficiary</p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        <x-beneficiary-switcher :beneficiary="$beneficiary" switchUrlPattern="/beneficiaries/__ID__/academic-records/create" formId="academic-form" />

        <form method="POST" action="/beneficiaries/{{ $beneficiary->id }}/academic-records"
            enctype="multipart/form-data" id="academic-form">
            @csrf

            <div id="academic-records-rows" class="space-y-4"></div>

            <!-- Attach Document (only for single) -->
            <div id="single-file-zone" class="rounded-xl overflow-hidden mt-6"
                style="border: 1px solid rgba(255,255,255,0.1);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                    style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Attach
                                Document (Optional)</h3>
                            <p class="text-xs text-white/50">Upload supporting files: Images or PDF (Max 5 MB) — only
                                when creating a single record</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <label class="block">
                        <div class="border-2 border-dashed rounded-lg p-6 text-center cursor-pointer transition-all duration-200"
                            style="border-color: rgba(255,255,255,0.2); background-color: rgba(255,255,255,0.03);"
                            onmouseenter="this.style.borderColor='var(--color-accent1)'; this.style.backgroundColor='rgba(255,255,255,0.1)'"
                            onmouseleave="this.style.borderColor='rgba(255,255,255,0.2)'; this.style.backgroundColor='rgba(255,255,255,0.03)'"
                            ondrop="handleDrop(event, 'academic_record_file', 'academic_record_selectedName', 'academic_record_fileName', 'academic_record_dropText')"
                            ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)"
                            id="academic_record_dropZone">
                            <input type="file" name="document_file" id="academic_record_file" class="hidden"
                                accept=".jpg,.jpeg,.png,.gif,.webp,.pdf"
                                onchange="updateDocumentFileName(this, 'academic_record_selectedName', 'academic_record_fileName', 'academic_record_dropText')">
                            <div id="academic_record_dropText">
                                <svg class="w-10 h-10 mx-auto mb-3" style="color: rgba(255,255,255,0.4);" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z">
                                    </path>
                                </svg>
                                <p class="text-sm font-medium" style="color: rgba(255,255,255,0.6);">Drop file here or
                                    click to select</p>
                                <p class="text-xs mt-1" style="color: rgba(255,255,255,0.4);">JPG, PNG, GIF, WebP, or
                                    PDF</p>
                            </div>
                            <div id="academic_record_fileName" class="hidden">
                                <p id="academic_record_selectedName" class="font-medium"
                                    style="color: var(--color-success);"></p>
                            </div>
                        </div>
                    </label>
                    <x-forms.errors name="document_file" />
                    <p id="bulk-file-hint" class="text-xs mt-2 hidden" style="color: rgba(255,255,255,0.5);">Documents
                        can be added after creation when creating multiple records. Remove extra rows to enable file
                        upload.</p>
                </div>
            </div>

            <div class="flex justify-start mt-4">
                <button type="button" id="add-academic-record"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02]"
                    style="font-family: var(--font-body1); background-color: transparent; color: var(--color-accent1); border: 1px solid var(--color-accent1);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Academic Record
                </button>
            </div>
            <x-forms.errors name="records" />

            <!-- Form Actions -->
            <div class="flex flex-wrap justify-end gap-3 pt-6 mt-4">
                <a href="/beneficiaries/{{ $beneficiary->id }}/academic-records"
                    class="px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] text-center"
                    style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                    style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                    Save & Go to List
                </button>
                <button type="button" id="save-switch-btn"
                    class="px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] inline-flex items-center gap-2"
                    style="font-family: var(--font-body1); background-color: transparent; color: var(--color-accent1); border: 1px solid var(--color-accent1);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4M4 17h12m0 0l-4-4m4 4l-4 4"></path></svg>
                    Save & Switch Beneficiary
                </button>
            </div>
        </form>
        <x-save-switch-dialog :beneficiary="$beneficiary" formId="academic-form" />
    </div>

    <template id="academic-record-template">
        <div class="record-card rounded-2xl overflow-hidden transition-all duration-300"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b flex items-center justify-between"
                style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
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
                        <h3 class="font-semibold text-white record-card-title"
                            style="font-family: var(--font-header1);">Academic Record #__NUMBER__</h3>
                        <p class="text-xs text-white/50">Enrollment, term and grades</p>
                    </div>
                </div>
                <button type="button"
                    class="remove-record inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-lg transition-all duration-200"
                    style="font-family: var(--font-body1); color: var(--color-danger); background-color: rgba(248,113,113,0.12); border: 1px solid rgba(248,113,113,0.2);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                    Remove
                </button>
            </div>
            <div class="p-6 space-y-6">
                <!-- Enrollment Context -->
                <div>
                    <label class="block mb-2 text-sm font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Enrollment <span
                            class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                    <div class="relative">
                        <select name="records[__INDEX__][enrollment_id]"
                            class="record-enrollment_id w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all appearance-none"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;"
                            required>
                            @foreach ($enrollmentsList as $enrollment)
                                <option value="{{ $enrollment->id }}" data-school="{{ $enrollment->school_name }}"
                                    data-year="{{ $enrollment->academic_year_start_date->format('Y') . '-' . $enrollment->academic_year_end_date->format('Y') }}"
                                    data-grade="{{ $enrollment->grade_level }}" style="color: var(--color-primary1);"
                                    @selected((string) $defaultEnrollmentId === (string) $enrollment->id)>
                                    {{ $enrollment->school_name }} - {{ $enrollment->grade_level }}
                                    ({{ $enrollment->academic_year_start_date->format('Y') }}-{{ $enrollment->academic_year_end_date->format('Y') }})
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
                        <div class="rounded-lg p-3"
                            style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                            <p class="text-xs uppercase tracking-wider mb-1" style="color: rgba(255,255,255,0.5);">
                                School</p>
                            <p class="record-school_display text-white text-sm font-medium"></p>
                        </div>
                        <div class="rounded-lg p-3"
                            style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                            <p class="text-xs uppercase tracking-wider mb-1" style="color: rgba(255,255,255,0.5);">
                                Academic Year</p>
                            <p class="record-year_display text-white text-sm font-medium"></p>
                        </div>
                        <div class="rounded-lg p-3"
                            style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                            <p class="text-xs uppercase tracking-wider mb-1" style="color: rgba(255,255,255,0.5);">
                                Grade</p>
                            <p class="record-grade_display text-white text-sm font-medium"></p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Term <span
                                class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                        <div class="relative">
                            <select name="records[__INDEX__][term]"
                                class="record-term w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all appearance-none"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;"
                                required>
                                <option value="" disabled selected style="color: var(--color-primary1);">Select
                                    a term</option>
                                <option value="1st" style="color: var(--color-primary1);">1st</option>
                                <option value="2nd" style="color: var(--color-primary1);">2nd</option>
                                <option value="3rd" style="color: var(--color-primary1);">3rd</option>
                                <option value="4th" style="color: var(--color-primary1);">4th</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Attendance (Percent)
                            <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                        <input type="number" step="0.01" min="0" max="100"
                            name="records[__INDEX__][school_attendance]"
                            class="record-attendance w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                            placeholder="Enter attendance" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">GWA (Percent)</label>
                        <div class="flex items-center gap-4 mb-3">
                            <span class="text-xs" style="color: rgba(255,255,255,0.5);">Auto-calculate GWA?</span>
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="records[__INDEX__][auto_calculate_gwa]" value="1"
                                    class="record-auto_yes w-3.5 h-3.5" style="accent-color: var(--color-accent1);"
                                    checked>
                                <span class="text-xs" style="color: rgba(255,255,255,0.7);">Yes</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="records[__INDEX__][auto_calculate_gwa]" value="0"
                                    class="record-auto_no w-3.5 h-3.5" style="accent-color: var(--color-accent1);">
                                <span class="text-xs" style="color: rgba(255,255,255,0.7);">No</span>
                            </label>
                        </div>
                        <input type="number" step="0.01" min="0" max="100"
                            name="records[__INDEX__][manual_gwa]"
                            class="record-manual_gwa w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                            placeholder="Enter GWA if no subject grades">
                    </div>
                </div>

                <!-- Subject Grades per record -->
                <div class="rounded-xl overflow-hidden" style="border: 1px solid rgba(255,255,255,0.1);">
                    <div class="px-4 py-3 border-b flex items-center justify-between"
                        style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-white" style="font-family: var(--font-header1);">
                                Subject Grades</p>
                            <p class="text-xs text-white/50">Add per subject</p>
                        </div>
                        <button type="button"
                            class="add-subject-grade inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02]"
                            style="font-family: var(--font-body1); background-color: transparent; color: var(--color-accent1); border: 1px solid var(--color-accent1);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Subject
                        </button>
                    </div>
                    <div class="p-4">
                        <div class="subject-grade-rows space-y-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <template id="subject-grade-template">
        <div class="subject-grade-row grid gap-3 rounded-lg p-4"
            style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
            <div class="grid grid-cols-1 md:grid-cols-[1fr_160px_auto] gap-3">
                <div>
                    <label class="block mb-1 text-xs font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.6);">Subject Name <span
                            class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                    <input name="records[__RINDEX__][subject_grades][__SINDEX__][subject_name]"
                        class="w-full px-3 py-2 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all text-sm"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                        placeholder="e.g., Math" required>
                </div>
                <div>
                    <label class="block mb-1 text-xs font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.6);">Grade <span
                            class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                    <input type="number" step="0.01" min="0" max="100"
                        name="records[__RINDEX__][subject_grades][__SINDEX__][grade]"
                        class="w-full px-3 py-2 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all text-sm"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                        placeholder="e.g., 90" required>
                </div>
                <div class="flex items-end">
                    <button type="button"
                        class="remove-subject-grade inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg transition-all duration-200"
                        style="font-family: var(--font-body1); color: var(--color-danger); background-color: rgba(248,113,113,0.12);">
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
            const existingRecords = @json(old('records', []));
            const rows = document.getElementById('academic-records-rows');
            const recordTemplate = document.getElementById('academic-record-template').innerHTML;
            const subjectTemplate = document.getElementById('subject-grade-template').innerHTML;
            const addBtn = document.getElementById('add-academic-record');
            const singleFileZone = document.getElementById('single-file-zone');
            const bulkHint = document.getElementById('bulk-file-hint');

            const toggleFileZone = () => {
                const count = rows.querySelectorAll('.record-card').length;
                if (count === 1) {
                    singleFileZone.style.display = '';
                    bulkHint.classList.add('hidden');
                } else {
                    singleFileZone.style.display = 'none';
                    bulkHint.classList.remove('hidden');
                }
            };

            const reindexRecords = () => {
                rows.querySelectorAll('.record-card').forEach((card, rIdx) => {
                    card.querySelector('.record-card-title').textContent = `Academic Record #${rIdx + 1}`;
                    // reindex main fields
                    card.querySelectorAll('input, select, textarea').forEach(el => {
                        if (!el.name) return;
                        // only reindex top-level records[old][...] not nested subject_grades inner? We'll handle subjects separately
                        // Replace first occurrence of records[d]
                        if (el.closest('.subject-grade-row')) return;
                        el.name = el.name.replace(/records\[\d+\]/, `records[${rIdx}]`);
                        // radio name fix
                        if (el.type === 'radio') el.name = `records[${rIdx}][auto_calculate_gwa]`;
                    });
                    // reindex subject rows within this card
                    const sgRows = card.querySelectorAll('.subject-grade-row');
                    sgRows.forEach((sgRow, sIdx) => {
                        sgRow.querySelectorAll('input').forEach(inp => {
                            inp.name =
                                `records[${rIdx}][subject_grades][${sIdx}][${inp.name.includes('subject_name') ? 'subject_name' : 'grade'}]`;
                        });
                    });
                    // also fix subject template placeholder for next adds (stored via data-index)
                    card.dataset.index = rIdx;
                    const rm = card.querySelector('.remove-record');
                    if (rm) rm.style.display = rows.querySelectorAll('.record-card').length > 1 ? '' :
                        'none';
                });
                toggleFileZone();
            };

            const syncEnrollmentDisplay = (card) => {
                const sel = card.querySelector('.record-enrollment_id');
                const opt = sel?.selectedOptions?.[0];
                const schoolEl = card.querySelector('.record-school_display');
                const yearEl = card.querySelector('.record-year_display');
                const gradeEl = card.querySelector('.record-grade_display');
                if (!opt || !opt.value) {
                    if (schoolEl) schoolEl.textContent = 'No enrollment';
                    if (yearEl) yearEl.textContent = '-';
                    if (gradeEl) gradeEl.textContent = '-';
                    return;
                }
                if (schoolEl) schoolEl.textContent = opt.dataset.school || '';
                if (yearEl) yearEl.textContent = opt.dataset.year || '';
                if (gradeEl) gradeEl.textContent = opt.dataset.grade || '';
            };

            const addSubjectRow = (card, subjectName = '', grade = '') => {
                const rIdx = parseInt(card.dataset.index, 10);
                const container = card.querySelector('.subject-grade-rows');
                const sIdx = container.querySelectorAll('.subject-grade-row').length;
                let html = subjectTemplate.replaceAll('__RINDEX__', rIdx).replaceAll('__SINDEX__', sIdx);
                container.insertAdjacentHTML('beforeend', html);
                const row = container.lastElementChild;
                row.querySelector('input[name$="[subject_name]"]').value = subjectName;
                row.querySelector('input[name$="[grade]"]').value = grade;
                row.querySelector('.remove-subject-grade').addEventListener('click', () => {
                    row.remove();
                    reindexRecords();
                });
            };

            const addRecord = (data = {}) => {
                if (rows.querySelectorAll('.record-card').length >= 20) return;
                const idx = rows.querySelectorAll('.record-card').length;
                let html = recordTemplate.replaceAll('__INDEX__', idx).replaceAll('__NUMBER__', idx + 1);
                rows.insertAdjacentHTML('beforeend', html);
                const card = rows.lastElementChild;
                card.dataset.index = idx;

                // populate if data provided
                if (data.enrollment_id) card.querySelector('.record-enrollment_id').value = data.enrollment_id;
                if (data.term) card.querySelector('.record-term').value = data.term;
                if (data.school_attendance) card.querySelector('.record-attendance').value = data.school_attendance;
                if (data.manual_gwa) card.querySelector('.record-manual_gwa').value = data.manual_gwa;
                if (data.auto_calculate_gwa !== undefined) {
                    const val = String(data.auto_calculate_gwa);
                    const yes = card.querySelector('.record-auto_yes');
                    const no = card.querySelector('.record-auto_no');
                    if (val === '0') {
                        no.checked = true;
                        yes.checked = false;
                    } else {
                        yes.checked = true;
                        no.checked = false;
                    }
                }

                // enrollment sync
                const enrollmentSelect = card.querySelector('.record-enrollment_id');
                enrollmentSelect.addEventListener('change', () => syncEnrollmentDisplay(card));
                syncEnrollmentDisplay(card);

                // subject grades existing
                const existingSubjects = data.subject_grades || [];
                existingSubjects.forEach(sg => addSubjectRow(card, sg.subject_name, sg.grade));

                // add subject button
                card.querySelector('.add-subject-grade').addEventListener('click', () => addSubjectRow(card));

                // remove record
                card.querySelector('.remove-record').addEventListener('click', () => {
                    card.remove();
                    reindexRecords();
                });

                reindexRecords();
            };

            addBtn.addEventListener('click', () => addRecord());

            if (existingRecords.length) {
                existingRecords.forEach(r => addRecord(r));
            } else {
                addRecord();
            }
        })();

        function handleDragOver(e) {
            e.preventDefault();
            e.currentTarget.classList.add('border-accent1', 'bg-white/5');
        }

        function handleDragLeave(e) {
            e.preventDefault();
            e.currentTarget.classList.remove('border-accent1', 'bg-white/5');
        }

        function handleDrop(e, inputId, selectedNameId, fileNameId, dropTextId) {
            e.preventDefault();
            const dz = e.currentTarget;
            dz.classList.remove('border-accent1', 'bg-white/5');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById(inputId).files = files;
                updateDocumentFileName({
                    files: files
                }, selectedNameId, fileNameId, dropTextId);
            }
        }

        function updateDocumentFileName(input, selectedNameId, fileNameId, dropTextId) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileName = file.name;
                const fileSize = (file.size / (1024 * 1024)).toFixed(2);
                if (fileSize > 5) {
                    alert('File size exceeds 5 MB limit');
                    input.value = '';
                    document.getElementById(fileNameId).classList.add('hidden');
                    document.getElementById(dropTextId).classList.remove('hidden');
                    return;
                }
                document.getElementById(fileNameId).classList.remove('hidden');
                document.getElementById(dropTextId).classList.add('hidden');
                document.getElementById(selectedNameId).textContent = `✓ ${fileName} (${fileSize} MB)`;
            }
        }
    </script>
</x-dashboardlayout>
