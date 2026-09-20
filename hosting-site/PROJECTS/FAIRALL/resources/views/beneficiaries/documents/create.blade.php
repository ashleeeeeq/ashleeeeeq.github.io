<x-dashboardlayout name="{{ $name }}" title="Upload Document">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Upload Document</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Add a document for the beneficiary</p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        <!-- Beneficiary Info Alert -->
        <div class="mb-6 p-4 rounded-xl flex items-center gap-3" style="background-color: rgba(255,204,51,0.1); border: 1px solid rgba(255,204,51,0.3);">
            <svg class="w-5 h-5" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="text-sm" style="color: var(--color-accent1); font-family: var(--font-body1);">
                Beneficiary: <strong>{{ $beneficiary->display_name }}</strong>
            </span>
        </div>

        @php
            $recordOptions = [
                'academic_record' => $academicRecords->map(fn ($record) => [
                    'id' => $record->id,
                    'label' => trim(($record->school_name ?? 'Academic Record') . ' - ' . ($record->academic_year ?? '') . ' ' . ($record->term ?? '')),
                ])->values(),
                'ffa_assessment' => $ffaAssessmentRecords->map(fn ($record) => [
                    'id' => $record->id,
                    'label' => 'FFA Assessment #' . $record->id . ' - ' . \Illuminate\Support\Carbon::parse($record->date)->format('M d, Y'),
                ])->values(),
                'injury_record' => $injuryRecords->map(fn ($record) => [
                    'id' => $record->id,
                    'label' => trim(($record->injury_type ?? 'Injury Record') . ' - ' . \Illuminate\Support\Carbon::parse($record->recovery_start_date)->format('M d, Y')),
                ])->values(),
            ];
        @endphp

        <form method="POST" action="{{ route('beneficiaries.documents.store', $beneficiary) }}" enctype="multipart/form-data">
            @csrf

            <!-- File Upload Section -->
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Upload File</h3>
                            <p class="text-xs text-white/50">Supported: Images (JPG, PNG, GIF, WebP), PDF (Max 5 MB)</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <label class="block">
                        <div class="border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition-all duration-200"
                             style="border-color: rgba(255,255,255,0.2); background-color: rgba(255,255,255,0.03);"
                             onmouseenter="this.style.borderColor='var(--color-accent1)'; this.style.backgroundColor='rgba(255,255,255,0.1)'"
                             onmouseleave="this.style.borderColor='rgba(255,255,255,0.2)'; this.style.backgroundColor='rgba(255,255,255,0.03)'"
                             ondrop="handleDrop(event)" 
                             ondragover="handleDragOver(event)" 
                             ondragleave="handleDragLeave(event)"
                             id="dropZone">
                            <input type="file" name="file[]" id="document_file" class="hidden" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf" multiple>
                            <div id="document_dropText">
                                <svg class="w-12 h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.3);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z"></path>
                                </svg>
                                <p class="text-sm font-medium" style="color: rgba(255,255,255,0.6);">Drop files here or click to select</p>
                                <p class="text-xs mt-2" style="color: rgba(255,255,255,0.4);">JPG, PNG, GIF, WebP, or PDF (Max 5 MB each)</p>
                            </div>
                            <div id="document_fileName" class="hidden">
                                <div id="document_fileList" class="space-y-1"></div>
                                <div class="flex gap-2 mt-2">
                                    <button type="button" id="document_addFiles" class="text-base text-accent1 hover:text-yellow-300 hidden">+ Add</button>
                                    <button type="button" id="document_clearFiles" class="text-base text-red-400 hover:text-red-300 hidden">- Clear</button>
                                </div>
                            </div>
                        </div>
                    </label>
                    <x-forms.errors name="file" />
                </div>
            </div>

            <!-- Link to Record Section -->
            <div class="rounded-xl overflow-hidden mt-6" style="border: 1px solid rgba(255,255,255,0.1);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Link to Record</h3>
                            <p class="text-xs text-white/50">Optionally link this document to a specific record</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Attach to a record</label>
                            <div class="relative">
                                <select name="link_to_module" class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all appearance-none" 
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;"
                                        onchange="handleModuleChange(this)">
                                    <option value="none" style="color: var(--color-primary1);" @selected(old('link_to_module', 'none') === 'none')>No record - Upload only for beneficiary</option>
                                    <option value="academic_record" style="color: var(--color-primary1);" @selected(old('link_to_module') === 'academic_record')>Academic Record</option>
                                    <option value="ffa_assessment" style="color: var(--color-primary1);" @selected(old('link_to_module') === 'ffa_assessment')>FFA Assessment</option>
                                    <option value="injury_record" style="color: var(--color-primary1);" @selected(old('link_to_module') === 'injury_record')>Injury Record</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                    </svg>
                                </div>
                            </div>
                            <x-forms.errors name="link_to_module" />
                        </div>

                        <!-- Dynamic Record Selection -->
                        <div id="recordIdField" class="{{ old('link_to_module', 'none') === 'none' ? 'hidden' : '' }}">
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Select Record</label>
                            <div class="relative">
                                <select name="record_id" id="record_id" class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1/50 transition-all appearance-none" 
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;">
                                    <option value="" style="color: var(--color-primary1);">-- Select a record --</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                    </svg>
                                </div>
                            </div>
                            <x-forms.errors name="record_id" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 pt-6 mt-4">
                <a href="{{ route('beneficiaries.documents.index', $beneficiary) }}" 
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

                <button type="submit" class="px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                    Upload Document
                </button>
            </div>
        </form>
    </div>

    <script>
        const recordOptions = @json($recordOptions);

        let documentFiles = [];

        function handleDragOver(e) {
            e.preventDefault();
            const dropZone = document.getElementById('dropZone');
            dropZone.style.borderColor = 'var(--color-accent1)';
            dropZone.style.backgroundColor = 'rgba(255,204,51,0.05)';
        }

        function handleDragLeave(e) {
            e.preventDefault();
            const dropZone = document.getElementById('dropZone');
            dropZone.style.borderColor = 'rgba(255,255,255,0.2)';
            dropZone.style.backgroundColor = 'rgba(255,255,255,0.03)';
        }

        function renderFileList() {
            const list = document.getElementById('document_fileList');
            const clearBtn = document.getElementById('document_clearFiles');
            const addBtn = document.getElementById('document_addFiles');
            list.innerHTML = '';
            if (documentFiles.length === 0) {
                document.getElementById('document_fileName').classList.add('hidden');
                document.getElementById('document_dropText').classList.remove('hidden');
                if (clearBtn) clearBtn.classList.add('hidden');
                if (addBtn) addBtn.classList.add('hidden');
                return;
            }
            document.getElementById('document_fileName').classList.remove('hidden');
            document.getElementById('document_dropText').classList.add('hidden');
            if (clearBtn) clearBtn.classList.remove('hidden');
            if (addBtn) addBtn.classList.remove('hidden');

            documentFiles.forEach((f, i) => {
                const size = (f.size / (1024 * 1024)).toFixed(2);
                const div = document.createElement('div');
                div.className = 'flex items-center justify-between gap-2 py-1 px-2 rounded text-sm';
                div.style.cssText = 'background-color: rgba(255,255,255,0.05);';
                div.innerHTML = `
                    <span style="color: var(--color-success);">\u2713 ${f.name} (${size} MB)</span>
                    <button type="button" data-index="${i}" class="remove-file-btn text-red-400 hover:text-red-300 font-bold leading-none text-2xl">\u00d7</button>
                `;
                list.appendChild(div);
            });

            list.querySelectorAll('.remove-file-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const idx = parseInt(this.dataset.index);
                    documentFiles.splice(idx, 1);
                    syncFileInput();
                    renderFileList();
                });
            });

            if (clearBtn) {
                clearBtn.onclick = function() {
                    documentFiles = [];
                    syncFileInput();
                    renderFileList();
                };
            }

            if (addBtn) {
                addBtn.onclick = function() {
                    document.getElementById('document_file').click();
                };
            }
        }

        function syncFileInput() {
            const input = document.getElementById('document_file');
            const dt = new DataTransfer();
            documentFiles.forEach(f => dt.items.add(f));
            input.files = dt.files;
        }

        function addFiles(files) {
            for (const f of files) {
                if ((f.size / (1024 * 1024)) > 5) {
                    alert(`File "${f.name}" exceeds 5 MB limit`);
                    continue;
                }
                documentFiles.push(f);
            }
            if (documentFiles.length > 0) {
                syncFileInput();
                renderFileList();
            }
        }

        function handleDrop(e) {
            e.preventDefault();
            const dropZone = document.getElementById('dropZone');
            dropZone.style.borderColor = 'rgba(255,255,255,0.2)';
            dropZone.style.backgroundColor = 'rgba(255,255,255,0.03)';
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                addFiles(files);
            }
        }

        document.getElementById('document_file')?.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                addFiles(this.files);
            }
        });

        function handleModuleChange(select) {
            const recordIdField = document.getElementById('recordIdField');
            const recordSelect = document.getElementById('record_id');

            if (select.value === 'none') {
                recordIdField.classList.add('hidden');
                recordSelect.innerHTML = '<option value="" style="color: var(--color-primary1);">-- Select a record --</option>';
            } else {
                recordIdField.classList.remove('hidden');
                recordSelect.innerHTML = '<option value="" style="color: var(--color-primary1);">-- Select a record --</option>';

                (recordOptions[select.value] || []).forEach((record) => {
                    const option = document.createElement('option');
                    option.value = record.id;
                    option.textContent = record.label;
                    option.style.color = 'var(--color-primary1)';
                    if (String(record.id) === @json(old('record_id'))) {
                        option.selected = true;
                    }
                    recordSelect.appendChild(option);
                });
            }
        }

        const moduleSelect = document.querySelector('select[name="link_to_module"]');
        handleModuleChange(moduleSelect);

    </script>
</x-dashboardlayout>