<x-dashboardlayout name="{{ $name }}" title="Create FFA Assessment">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Create FFA
                    Assessment</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Add fairplay assessment(s)
                    for {{ $beneficiary->display_name }}</p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        <x-beneficiary-switcher :beneficiary="$beneficiary" switchUrlPattern="/beneficiaries/__ID__/ffa-assessments/create" formId="ffa-form" />

        <form method="POST" action="/beneficiaries/{{ $beneficiary->id }}/ffa-assessments"
            enctype="multipart/form-data" id="ffa-form">
            @csrf

            <div id="ffa-rows" class="space-y-4"></div>

            <!-- Attach Document Card - only for single -->
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
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Attach
                                Document</h2>
                            <p class="text-xs text-white/50">Upload supporting files: Images or PDF (Max 5 MB) — only
                                when creating a single record</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <label class="block">
                        <div class="border-2 border-dashed rounded-lg p-4 text-center cursor-pointer transition-all"
                            style="border-color: rgba(255,255,255,0.2); background-color: rgba(255,255,255,0.03);"
                            onmouseenter="this.style.borderColor='var(--color-accent1)'; this.style.backgroundColor='rgba(255,255,255,0.1)'"
                            onmouseleave="this.style.borderColor='rgba(255,255,255,0.2)'; this.style.backgroundColor='rgba(255,255,255,0.03)'"
                            ondrop="handleDrop(event, 'ffa_assessment_file', 'ffa_assessment_selectedName', 'ffa_assessment_fileName', 'ffa_assessment_dropText')"
                            ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)"
                            id="ffa_assessment_dropZone">
                            <input type="file" name="document_file" id="ffa_assessment_file" class="hidden"
                                accept=".jpg,.jpeg,.png,.gif,.webp,.pdf"
                                onchange="updateDocumentFileName(this, 'ffa_assessment_selectedName', 'ffa_assessment_fileName', 'ffa_assessment_dropText')">
                            <div id="ffa_assessment_dropText" class="pointer-events-none">
                                <svg class="w-8 h-8 mx-auto mb-2" style="color: rgba(255,255,255,0.4);" fill="none"
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
                            <div id="ffa_assessment_fileName" class="hidden">
                                <p id="ffa_assessment_selectedName" class="font-medium"
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
                <button type="button" id="add-ffa"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02]"
                    style="font-family: var(--font-body1); background-color: transparent; color: var(--color-accent1); border: 1px solid var(--color-accent1);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Assessment
                </button>
            </div>
            <x-forms.errors name="assessments" />

            <!-- Form Actions -->
            <div class="flex flex-wrap justify-end gap-3 pt-6 mt-6 border-t" style="border-color: rgba(255,255,255,0.1);">
                <a href="/beneficiaries/{{ $beneficiary->id }}/ffa-assessments"
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
        <x-save-switch-dialog :beneficiary="$beneficiary" formId="ffa-form" />
    </div>

    <template id="ffa-template">
        <div class="ffa-card rounded-2xl overflow-hidden transition-all duration-300"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b flex items-center justify-between"
                style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
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
                        <h2 class="font-semibold text-white ffa-card-title" style="font-family: var(--font-header1);">
                            Assessment #__NUMBER__</h2>
                        <p class="text-xs text-white/50">Fairplay assessment information</p>
                    </div>
                </div>
                <button type="button"
                    class="remove-ffa inline-flex items-center gap-1.5 px-3 py-1.5 text-sm rounded-lg transition-all duration-200"
                    style="font-family: var(--font-body1); color: var(--color-danger); background-color: rgba(248,113,113,0.12); border: 1px solid rgba(248,113,113,0.2);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                    Remove
                </button>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Category <span
                                class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                        <div class="relative">
                            <select name="assessments[__INDEX__][assessment_category_id]"
                                class="ffa-category w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;"
                                required>
                                @foreach ($categories as $id => $label)
                                    <option value="{{ $id }}" style="color: var(--color-primary1);">
                                        {{ $label }}</option>
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
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Name
                            (Optional)</label>
                        <input type="text" name="assessments[__INDEX__][name]"
                            class="ffa-name w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                            placeholder="Enter assessment name">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Score <span
                                    class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="number" step="any" name="assessments[__INDEX__][score]"
                                class="ffa-score w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                                placeholder="Enter score" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-semibold"
                                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Max Score <span
                                    class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="number" step="any" name="assessments[__INDEX__][max_score]"
                                class="ffa-max_score w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                                value="100" placeholder="Enter max score" required>
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Assessment Date <span
                                class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                        <input type="date" name="assessments[__INDEX__][date]"
                            class="ffa-date w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                            required>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-semibold"
                            style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Remarks
                            (Optional)</label>
                        <textarea name="assessments[__INDEX__][remarks]" rows="2"
                            class="ffa-remarks w-full px-4 py-2.5 text-white rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all resize-y"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                            placeholder="Optional remarks"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <script>
        (() => {
            const existing = @json(old('assessments', []));
            const rows = document.getElementById('ffa-rows');
            const template = document.getElementById('ffa-template').innerHTML;
            const addBtn = document.getElementById('add-ffa');
            const singleFileZone = document.getElementById('single-file-zone');
            const bulkHint = document.getElementById('bulk-file-hint');

            const toggleFileZone = () => {
                const count = rows.querySelectorAll('.ffa-card').length;
                if (count === 1) {
                    singleFileZone.style.display = '';
                    singleFileZone.style.opacity = '1';
                    bulkHint.classList.add('hidden');
                } else {
                    singleFileZone.style.display = 'none';
                    bulkHint.classList.remove('hidden');
                }
            };

            const reindex = () => {
                rows.querySelectorAll('.ffa-card').forEach((card, idx) => {
                    card.querySelector('.ffa-card-title').textContent = `Assessment #${idx + 1}`;
                    card.querySelectorAll('input, select, textarea').forEach(el => {
                        if (el.name) el.name = el.name.replace(/assessments\[\d+\]/,
                            `assessments[${idx}]`);
                    });
                    const rm = card.querySelector('.remove-ffa');
                    if (rm) rm.style.display = rows.querySelectorAll('.ffa-card').length > 1 ? '' : 'none';
                });
                toggleFileZone();
            };

            const addRow = (data = {}) => {
                if (rows.querySelectorAll('.ffa-card').length >= 20) return;
                const idx = rows.querySelectorAll('.ffa-card').length;
                const html = template.replaceAll('__INDEX__', idx).replaceAll('__NUMBER__', idx + 1);
                rows.insertAdjacentHTML('beforeend', html);
                const card = rows.lastElementChild;
                if (data.assessment_category_id) card.querySelector('.ffa-category').value = data
                    .assessment_category_id;
                if (data.name) card.querySelector('.ffa-name').value = data.name;
                if (data.score) card.querySelector('.ffa-score').value = data.score;
                if (data.max_score) card.querySelector('.ffa-max_score').value = data.max_score;
                if (data.date) card.querySelector('.ffa-date').value = data.date;
                if (data.remarks) card.querySelector('.ffa-remarks').value = data.remarks;
                card.querySelector('.remove-ffa').addEventListener('click', () => {
                    card.remove();
                    reindex();
                });
                reindex();
            };

            addBtn.addEventListener('click', () => addRow());
            if (existing.length) existing.forEach(r => addRow(r));
            else addRow();
        })();

        function handleDragOver(e) {
            e.preventDefault();
            e.currentTarget.classList.add('border-accent1', 'bg-base-100');
        }

        function handleDragLeave(e) {
            e.preventDefault();
            e.currentTarget.classList.remove('border-accent1', 'bg-base-100');
        }

        function handleDrop(e, inputId, selectedNameId, fileNameId, dropTextId) {
            e.preventDefault();
            const dz = e.currentTarget;
            dz.classList.remove('border-accent1', 'bg-base-100');
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
