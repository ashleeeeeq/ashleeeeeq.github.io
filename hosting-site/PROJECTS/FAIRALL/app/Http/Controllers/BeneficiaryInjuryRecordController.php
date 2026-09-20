<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\InjuryRecord;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BeneficiaryInjuryRecordController extends Controller
{
    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    public function index(Beneficiary $beneficiary): View
    {
        $beneficiary->load(['injuryRecords']);

        $injuryRecords = $beneficiary->injuryRecords
            ->sortByDesc(fn($record) => [$record->recovery_start_date, $record->id])
            ->values();

        return view('beneficiaries.injury_records.index', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'injuryRecords' => $injuryRecords,
        ]);
    }

    public function create(Beneficiary $beneficiary): View
    {
        return view('beneficiaries.injury_records.create', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
        ]);
    }

    public function store(Request $request, Beneficiary $beneficiary): RedirectResponse
    {
        $validated = $request->validate([
            'injury_type' => ['required', 'string'],
            'severity' => ['required', 'in:minor,moderate,serious,severe,critical'],
            'body_part' => ['required', 'string'],
            'status' => ['required', 'in:recovering,recovered,chronic'],
            'recovery_start_date' => ['required', 'date'],
            'recovery_end_date' => ['nullable', 'date'],
            'document_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf', 'max:5120'],
        ]);

        $record = DB::transaction(function () use ($beneficiary, $validated, $request) {
            $record = $beneficiary->injuryRecords()->create([
                'injury_type' => $validated['injury_type'],
                'severity' => $validated['severity'],
                'body_part' => $validated['body_part'],
                'status' => $validated['status'],
                'recovery_start_date' => $validated['recovery_start_date'],
                'recovery_end_date' => $validated['recovery_end_date'] ?? null,
                'created_by' => Auth::user()?->staff?->id,
            ]);

            if ($request->hasFile('document_file')) {
                $this->fileUploadService->uploadFile(
                    file: $request->file('document_file'),
                    beneficiary_id: $beneficiary->id,
                    source_module: 'injury_record',
                    source_record_id: $record->id,
                    uploaded_by: Auth::id()
                );
            }

            return $record;
        });

        return redirect("/beneficiaries/{$beneficiary->id}/injury-records")->with('status', 'Injury record added.');
    }

    public function edit(Beneficiary $beneficiary, InjuryRecord $injuryRecord): View
    {
        return view('beneficiaries.injury_records.edit', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'record' => $injuryRecord,
        ]);
    }

    public function show(Beneficiary $beneficiary, InjuryRecord $injuryRecord): View
    {
        abort_if($injuryRecord->beneficiary_id !== $beneficiary->id, 404);

        return view('beneficiaries.injury_records.show', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'record' => $injuryRecord,
        ]);
    }

    public function update(Request $request, Beneficiary $beneficiary, InjuryRecord $injuryRecord): RedirectResponse
    {
        $validated = $request->validate([
            'injury_type' => ['required', 'string'],
            'severity' => ['required', 'in:minor,moderate,serious,severe,critical'],
            'body_part' => ['required', 'string'],
            'status' => ['required', 'in:recovering,recovered,chronic'],
            'recovery_start_date' => ['required', 'date'],
            'recovery_end_date' => ['nullable', 'date'],
        ]);

        $injuryRecord->update(array_merge($validated, ['updated_by' => Auth::user()?->staff?->id]));

        return redirect("/beneficiaries/{$beneficiary->id}/injury-records")->with('status', 'Injury record updated.');
    }

    public function destroy(Beneficiary $beneficiary, InjuryRecord $injuryRecord): RedirectResponse
    {
        $injuryRecord->delete();

        return redirect("/beneficiaries/{$beneficiary->id}/injury-records")->with('status', 'Injury record deleted.');
    }
}
