<x-dashboardlayout name="{{ $name }}" title="Edit Document">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Edit Document</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Update document information for {{ $beneficiary->display_name }}</p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        <form method="POST" action="{{ route('beneficiaries.documents.update', [$beneficiary, $document]) }}">
            @csrf
            @method('PUT')

            <!-- Document Information Card -->
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Document Information</h2>
                            <p class="text-xs text-white/50">View current document details</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Original Filename -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Original Filename</label>
                            <div class="w-full px-4 py-2.5 rounded-lg border bg-white/5 flex items-center cursor-not-allowed"
                                 style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.2); color: rgba(255,255,255,0.7);">
                                {{ basename($document->file_path) ?: 'Unknown' }}
                            </div>
                        </div>

                        <!-- File Type -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">File Type</label>
                            <div class="w-full px-4 py-2.5 rounded-lg border bg-white/5 flex items-center cursor-not-allowed"
                                 style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.2); color: rgba(255,255,255,0.7);">
                                {{ $document->file_type }}
                            </div>
                        </div>

                        <!-- File Size -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">File Size</label>
                            <div class="w-full px-4 py-2.5 rounded-lg border bg-white/5 flex items-center cursor-not-allowed"
                                 style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.2); color: rgba(255,255,255,0.7);">
                                {{ \App\Services\FileUploadService::formatFileSize($document->file_size) }}
                            </div>
                        </div>

                        <!-- Uploaded Date -->
                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Uploaded</label>
                            <div class="w-full px-4 py-2.5 rounded-lg border bg-white/5 flex items-center cursor-not-allowed"
                                 style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.2); color: rgba(255,255,255,0.7);">
                                {{ $document->created_at->format('F j, Y \a\t g:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Details Card -->
            <div class="rounded-xl overflow-hidden mt-6" style="border: 1px solid rgba(255,255,255,0.1);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Edit Details</h2>
                            <p class="text-xs text-white/50">Update document display information</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div>
                        <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Display Name <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                        <input type="text" name="display_name" class="w-full px-4 py-2.5 text-primary1 rounded-lg border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all" 
                               style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);"
                               value="{{ old('display_name', $document->display_name) }}" required>
                        <p class="text-xs mt-1" style="color: rgba(255,255,255,0.4);">The name shown in the document list (doesn't change the stored file)</p>
                        <x-forms.errors name="display_name" />
                    </div>
                </div>
            </div>

            <!-- Source Information Card (if linked) -->
            @if ($document->source_module)
                <div class="rounded-xl overflow-hidden mt-6" style="border: 1px solid rgba(255,255,255,0.1);">
                    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                                <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Source Information</h2>
                                <p class="text-xs text-white/50">This document is linked to a specific record</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div>
                            <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Linked Record</label>
                            <div class="w-full px-4 py-2.5 rounded-lg border bg-white/5 flex items-center cursor-not-allowed"
                                 style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.2); color: rgba(255,255,255,0.7);">
                                <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" style="background-color: rgba(255,204,51,0.15); color: var(--color-accent1);">
                                    {{ match ($document->source_module) {
                                        'academic_record' => 'Academic Record',
                                        'ffa_assessment' => 'FFA Assessment',
                                        'injury_record' => 'Injury Record',
                                        default => ucfirst(str_replace('_', ' ', $document->source_module)),
                                    } }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 pt-6 mt-6 border-t" style="border-color: rgba(255,255,255,0.1);">
                <a href="{{ route('beneficiaries.documents.index', $beneficiary) }}" 
                   class="px-6 py-2.5 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] text-center"
                    style="font-family: var(--font-body1); 
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
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-dashboardlayout>