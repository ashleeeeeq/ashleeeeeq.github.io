<?php

namespace App\Http\Controllers;

use App\Models\AcademicRecord;
use App\Models\Beneficiary;
use App\Models\FfaAssessmentRecord;
use App\Models\Document;
use App\Models\InjuryRecord;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    /**
     * Display all documents for a beneficiary.
     */
    public function index(Beneficiary $beneficiary): View
    {
        $documents = $beneficiary->documents()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('beneficiaries.documents.index', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'documents' => $documents,
        ]);
    }

    /**
     * Show the form to upload a new document.
     */
    public function create(Beneficiary $beneficiary): View
    {
        return view('beneficiaries.documents.create', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'academicRecords' => AcademicRecord::query()
                ->where('beneficiary_id', $beneficiary->id)
                ->with('educationEnrollment')
                ->orderByDesc('id')
                ->get(),
            'ffaAssessmentRecords' => FfaAssessmentRecord::query()
                ->where('beneficiary_id', $beneficiary->id)
                ->orderByDesc('id')
                ->get(['id', 'date', 'name', 'assessment_category_id']),
            'injuryRecords' => InjuryRecord::query()
                ->where('beneficiary_id', $beneficiary->id)
                ->orderByDesc('id')
                ->get(['id', 'injury_type', 'recovery_start_date']),
        ]);
    }

    /**
     * Store a newly uploaded document.
     */
    public function store(Request $request, Beneficiary $beneficiary): RedirectResponse
    {
        $validated = $request->validate([
            'file' => 'required|array',
            'file.*' => 'required|file|mimes:jpg,jpeg,png,gif,webp,pdf|max:5120',
            'link_to_module' => 'nullable|in:none,academic_record,ffa_assessment,injury_record',
            'record_id' => 'nullable|integer',
        ]);

        $files = $request->file('file');
        $sourceModule = $validated['link_to_module'] !== 'none' ? $validated['link_to_module'] : null;
        $sourceRecordId = ($sourceModule && $validated['record_id']) ? $validated['record_id'] : null;

        if ($sourceModule !== null) {
            $recordExists = match ($sourceModule) {
                'academic_record' => AcademicRecord::query()
                    ->where('beneficiary_id', $beneficiary->id)
                    ->where('id', $sourceRecordId)
                    ->exists(),
                'ffa_assessment' => FfaAssessmentRecord::query()
                    ->where('beneficiary_id', $beneficiary->id)
                    ->where('id', $sourceRecordId)
                    ->exists(),
                'injury_record' => InjuryRecord::query()
                    ->where('beneficiary_id', $beneficiary->id)
                    ->where('id', $sourceRecordId)
                    ->exists(),
                default => false,
            };

            if (!$recordExists) {
                return redirect()->back()
                    ->withErrors(['record_id' => 'Please select a valid record for the chosen module.'])
                    ->withInput();
            }
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($files as $file) {
            $validation = $this->fileUploadService->validate($file);

            if (!$validation['valid']) {
                $failCount++;
                continue;
            }

            $document = $this->fileUploadService->uploadFile(
                file: $file,
                beneficiary_id: $beneficiary->id,
                display_name: null,
                source_module: $sourceModule,
                source_record_id: $sourceRecordId,
                uploaded_by: Auth::id()
            );

            if ($document) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        if ($successCount === 0) {
            return redirect()->back()
                ->withErrors(['file' => 'Failed to upload files. Please try again.'])
                ->withInput();
        }

        $message = "$successCount document(s) uploaded successfully.";
        if ($failCount > 0) {
            $message .= " $failCount file(s) failed.";
        }

        return redirect()->route('beneficiaries.documents.index', $beneficiary)
            ->with('success', $message);
    }

    /**
     * Show the form to edit a document's metadata.
     */
    public function edit(Beneficiary $beneficiary, Document $document): View
    {
        return view('beneficiaries.documents.edit', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'document' => $document,
        ]);
    }

    /**
     * Update a document's metadata.
     */
    public function update(Request $request, Beneficiary $beneficiary, Document $document): RedirectResponse
    {
        $validated = $request->validate([
            'display_name' => 'required|string|max:255',
        ]);

        $document->update($validated);

        return redirect()->route('beneficiaries.documents.index', $beneficiary)
            ->with('success', 'Document updated successfully.');
    }

    /**
     * Delete a document.
     */
    public function destroy(Beneficiary $beneficiary, Document $document): RedirectResponse
    {
        $document->deleteFile();

        return redirect()->route('beneficiaries.documents.index', $beneficiary)
            ->with('success', 'Document deleted successfully.');
    }

    /**
     * Download a document (with signed URL).
     */
    public function download(Beneficiary $beneficiary, Document $document)
    {
        $url = $document->getDownloadUrl();
        return redirect($url);
    }
}
