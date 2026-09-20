<x-dashboardlayout name="{{ $name }}" title="Create Injury Record">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Create Injury Record</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Add a new injury record for {{ $beneficiary->display_name }}</p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        <form method="POST" action="/beneficiaries/{{ $beneficiary->id }}/injury-records" enctype="multipart/form-data">
            @csrf

            <!-- Injury Details Card -->
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Injury Details</h2>
                            <p class="text-xs text-white/50">Enter the injury and recovery information</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Injury Type -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Injury Type <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="text" name="injury_type" class="w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all" 
                                   style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                                   value="{{ old('injury_type') }}" placeholder="e.g., Fracture, Sprain, Concussion" required>
                            <x-forms.errors name="injury_type" />
                        </div>

                        <!-- Severity -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Severity <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <div class="relative">
                                <select name="severity" class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none" 
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;" required>
                                    <option value="" disabled @selected(empty(old('severity'))) style="color: var(--color-primary1);">Select severity</option>
                                    <option value="minor" @selected(old('severity') === 'minor') style="color: var(--color-primary1);">Minor</option>
                                    <option value="moderate" @selected(old('severity') === 'moderate') style="color: var(--color-primary1);">Moderate</option>
                                    <option value="serious" @selected(old('severity') === 'serious') style="color: var(--color-primary1);">Serious</option>
                                    <option value="severe" @selected(old('severity') === 'severe') style="color: var(--color-primary1);">Severe</option>
                                    <option value="critical" @selected(old('severity') === 'critical') style="color: var(--color-primary1);">Critical</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                    </svg>
                                </div>
                            </div>
                            <x-forms.errors name="severity" />
                        </div>

                        <!-- Body Part -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Body Part <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="text" name="body_part" class="w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all" 
                                   style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                                   value="{{ old('body_part') }}" placeholder="e.g., Left Ankle, Right Wrist" required>
                            <x-forms.errors name="body_part" />
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Status <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <div class="relative">
                                <select name="status" class="w-full px-4 py-2.5 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all appearance-none" 
                                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white;" required>
                                    <option value="" disabled @selected(empty(old('status'))) style="color: var(--color-primary1);">Select status</option>
                                    <option value="recovering" @selected(old('status') === 'recovering') style="color: var(--color-primary1);">Recovering</option>
                                    <option value="recovered" @selected(old('status') === 'recovered') style="color: var(--color-primary1);">Recovered</option>
                                    <option value="chronic" @selected(old('status') === 'chronic') style="color: var(--color-primary1);">Chronic</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                                    </svg>
                                </div>
                            </div>
                            <x-forms.errors name="status" />
                        </div>

                        <!-- Recovery Dates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Recovery Start Date <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="date" name="recovery_start_date" class="w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all" 
                                   style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                                   value="{{ old('recovery_start_date') }}" required>
                                <x-forms.errors name="recovery_start_date" />
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Recovery End Date (Optional)</label>
                                <input type="date" name="recovery_end_date" class="w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all" 
                                       style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                                       value="{{ old('recovery_end_date') }}">
                                <x-forms.errors name="recovery_end_date" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attach Document Card -->
            <div class="rounded-xl overflow-hidden mt-6" style="border: 1px solid rgba(255,255,255,0.1);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Attach Document</h2>
                            <p class="text-xs text-white/50">Upload supporting files: Images or PDF (Max 5 MB)</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <label class="block">
                        <div class="border-2 border-dashed rounded-lg p-4 text-center cursor-pointer transition-all"
                            style="border-color: rgba(255,255,255,0.2); background-color: rgba(255,255,255,0.03);"
                            onmouseenter="this.style.borderColor='var(--color-accent1)'; this.style.backgroundColor='rgba(255,255,255,0.1)'"
                            onmouseleave="this.style.borderColor='rgba(255,255,255,0.2)'; this.style.backgroundColor='rgba(255,255,255,0.03)'"
                            ondrop="handleDrop(event, 'injury_record_file', 'injury_record_selectedName', 'injury_record_fileName', 'injury_record_dropText')" 
                            ondragover="handleDragOver(event)"
                            ondragleave="handleDragLeave(event)" 
                            id="injury_record_dropZone">
                            <input type="file" name="document_file" id="injury_record_file" class="hidden"
                                accept=".jpg,.jpeg,.png,.gif,.webp,.pdf" 
                                onchange="updateDocumentFileName(this, 'injury_record_selectedName', 'injury_record_fileName', 'injury_record_dropText')">
                            <div id="injury_record_dropText" class="pointer-events-none">
                                <svg class="w-8 h-8 mx-auto mb-2" style="color: rgba(255,255,255,0.4);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="text-sm font-medium" style="color: rgba(255,255,255,0.6);">Drop file here or click to select</p>
                                <p class="text-xs mt-1" style="color: rgba(255,255,255,0.4);">JPG, PNG, GIF, WebP, or PDF</p>
                            </div>
                            <div id="injury_record_fileName" class="hidden">
                                <p id="injury_record_selectedName" class="font-medium" style="color: var(--color-success);"></p>
                            </div>
                        </div>
                    </label>
                    <x-forms.errors name="document_file" />
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 pt-6 mt-6 border-t" style="border-color: rgba(255,255,255,0.1);">
                <a href="/beneficiaries/{{ $beneficiary->id }}/injury-records" 
                   class="px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] text-center"
                    style="
                        font-family: var(--font-body1); 
                        background-color: transparent; 
                        color: var(--color-danger); 
                        border: 1px solid var(--color-danger);"
                    onmouseover="
                        this.style.backgroundColor='var(--color-danger-light)'; 
                        this.style.color='var(--color-danger-dark)'; 
                        this.style.borderColor='var(--color-danger-dark)';"
                    onmouseout="
                        this.style.backgroundColor='transparent'; 
                        this.style.color='var(--color-danger)'; 
                        this.style.borderColor='var(--color-danger)';">
                        Cancel
                </a>

                <button type="submit" 
                        class="px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                    Save Injury Record
                </button>
            </div>
        </form>
    </div>

    <script>
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
            const dropZone = e.currentTarget;
            dropZone.classList.remove('border-accent1', 'bg-base-100');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById(inputId).files = files;
                updateDocumentFileName({ files: files }, selectedNameId, fileNameId, dropTextId);
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