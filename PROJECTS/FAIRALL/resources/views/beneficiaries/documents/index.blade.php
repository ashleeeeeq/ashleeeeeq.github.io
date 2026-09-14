<x-dashboardlayout name="{{ $name }}" title="Documents">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Documents</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Manage all documents for the beneficiary</p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        @include('beneficiaries.partials.profile-tabs')

        <!-- Documents Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Documents</h2>
                            <p class="text-xs text-white/50">Manage all documents for {{ $beneficiary->display_name }}</p>
                        </div>
                    </div>
                    <a href="/beneficiaries/{{ $beneficiary->id }}/documents/create" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap"
                    style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                        </svg>
                        Add Document
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if ($documents->isEmpty())
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto mb-4" style="color: rgba(255,255,255,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path>
                        </svg>
                        <p class="text-sm" style="color: rgba(255,255,255,0.4);">No documents uploaded yet.</p>
                        <p class="text-xs mt-1" style="color: rgba(255,255,255,0.3);">Click "Add Document" to upload your first file.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-225">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">File Name</th>
                                    <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Type</th>
                                    <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Size</th>
                                    <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Source</th>
                                    <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Uploaded</th>
                                    <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documents as $index => $doc)
                                    <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                        <td class="px-3 sm:px-4 py-3 align-middle">
                                            <div class="flex items-center gap-3">
                                                @if ($doc->isImage())
                                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(59,130,246,0.15);">
                                                        <svg class="w-4 h-4" style="color: #60a5fa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                @elseif ($doc->isPdf())
                                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(239,68,68,0.15);">
                                                        <svg class="w-4 h-4" style="color: #f87171;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,255,255,0.1);">
                                                        <svg class="w-4 h-4" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="font-medium text-white">{{ $doc->display_name }}</div>
                                                    <div class="text-xs" style="color: rgba(255,255,255,0.5);">{{ $doc->file_type }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <td class="px-3 sm:px-4 py-3 align-middle text-center">
                                            @if ($doc->isImage())
                                                <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" style="background-color: rgba(59,130,246,0.15); color: #60a5fa;">Image</span>
                                            @elseif ($doc->isPdf())
                                                <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" style="background-color: rgba(239,68,68,0.15); color: #f87171;">PDF</span>
                                            @else
                                                <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" style="background-color: rgba(255,255,255,0.1); color: rgba(255,255,255,0.6);">File</span>
                                            @endif
                                        </div>
                                        <td class="px-3 sm:px-4 py-3 align-middle text-center text-sm" style="color: rgba(255,255,255,0.7);">{{ \App\Services\FileUploadService::formatFileSize($doc->file_size) }}</div>
                                        <td class="px-3 sm:px-4 py-3 align-middle text-center">
                                            @if ($doc->source_module)
                                                <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" style="background-color: rgba(255,204,51,0.15); color: var(--color-accent1);">
                                                    {{ match ($doc->source_module) {
                                                        'academic_record' => 'Academic Record',
                                                        'ffa_assessment' => 'FFA Assessment',
                                                        'injury_record' => 'Injury Record',
                                                        default => ucfirst(str_replace('_', ' ', $doc->source_module)),
                                                    } }}
                                                </span>
                                            @else
                                                <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" style="background-color: rgba(255,255,255,0.1); color: rgba(255,255,255,0.6);">Manual Upload</span>
                                            @endif
                                        </div>
                                        <td class="px-3 sm:px-4 py-3 align-middle text-center text-sm" style="color: rgba(255,255,255,0.7);">{{ $doc->created_at->format('M d, Y') }}</div>
                                        <td class="px-3 sm:px-4 py-3 align-middle text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <!-- Download -->
                                                <a href="{{ route('beneficiaries.documents.download', [$beneficiary, $doc]) }}" 
                                                   class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-white/10 hover:scale-110" title="Download">
                                                    <svg class="w-4 h-4" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                    </svg>
                                                </a>

                                                <!-- Preview (for images and PDFs) -->
                                                @if ($doc->isImage() || $doc->isPdf())
                                                    <button class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-white/10 hover:scale-110" title="Preview"
                                                            onclick="document.getElementById('previewModal_{{ $doc->id }}').showModal()">
                                                        <svg class="w-4 h-4" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </button>
                                                    <!-- Preview Modal -->
                                                    <dialog id="previewModal_{{ $doc->id }}" class="modal">
                                                        <div class="modal-box w-11/12 max-w-4xl bg-white rounded-xl p-6">
                                                            <h3 class="text-xl font-bold mb-4" style="color: var(--color-primary1);">{{ $doc->display_name }}</h3>
                                                            <div class="py-4">
                                                                @if ($doc->isImage())
                                                                    <img src="{{ $doc->getPreviewUrl() }}" alt="{{ $doc->display_name }}" class="max-w-full max-h-96 mx-auto rounded">
                                                                @elseif ($doc->isPdf())
                                                                    <iframe src="{{ $doc->getPreviewUrl() }}" class="w-full h-96 rounded border"></iframe>
                                                                @endif
                                                            </div>
                                                            <div class="flex justify-end gap-3 mt-4">
                                                                <a href="{{ route('beneficiaries.documents.download', [$beneficiary, $doc]) }}" 
                                                                class="px-4 py-2 font-semibold rounded-lg transition-all duration-200 hover:scale-[1.02]"
                                                                style="background-color: var(--color-accent1); color: var(--color-primary1);">Download</a>
                                                                <button onclick="document.getElementById('previewModal_{{ $doc->id }}').close()" 
                                                                        class="px-4 py-2 font-semibold rounded-lg transition-all duration-200 hover:scale-[1.02]"
                                                                        style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                                                                        onmouseover="this.style.backgroundColor='var(--color-danger-light)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)';"
                                                                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-danger)'; this.style.borderColor='var(--color-danger)';">Close</button>
                                                            </div>
                                                        </div>
                                                        <div class="modal-backdrop bg-black/50" onclick="document.getElementById('previewModal_{{ $doc->id }}').close()"></div>
                                                    </dialog>
                                                    @endif

                                                <!-- Edit -->
                                                <a href="{{ route('beneficiaries.documents.edit', [$beneficiary, $doc]) }}" 
                                                   class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-white/10 hover:scale-110" title="Edit">
                                                    <svg class="w-4 h-4" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                                                    </svg>
                                                </a>

                                                <!-- Delete -->
                                                <button class="p-1.5 rounded-lg transition-all hover:bg-red-500/20" title="Delete"
                                                        onclick="document.getElementById('deleteModal_{{ $doc->id }}').showModal()">
                                                    <svg class="w-4 h-4" style="color: var(--color-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                                                    </svg>
                                                </button>

                                                <x-confirm-dialog id="deleteModal_{{ $doc->id }}"
                                                    title="Delete Document"
                                                    message="Are you sure you want to delete <strong>{{ $doc->display_name }}</strong>? This action cannot be undone."
                                                    deleteUrl="{{ route('beneficiaries.documents.destroy', [$beneficiary, $doc]) }}" />
                                            </div>
                                        </div>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($documents->hasPages())
                        <div class="flex justify-center mt-6">
                            {{ $documents->links('pagination.custom') }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-dashboardlayout>