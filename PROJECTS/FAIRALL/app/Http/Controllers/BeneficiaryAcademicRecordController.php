<?php

namespace App\Http\Controllers;

use App\Models\AcademicRecord;
use App\Models\Beneficiary;
use App\Models\EducationEnrollment;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BeneficiaryAcademicRecordController extends Controller
{
    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    private function enrollmentContext(Beneficiary $beneficiary, ?int $enrollmentId = null): ?EducationEnrollment
    {
        if (!$enrollmentId) {
            return null;
        }

        return $beneficiary->educationEnrollments()->whereKey($enrollmentId)->first();
    }

    public function index(Beneficiary $beneficiary): View
    {
        $beneficiary->load('educationEnrollments');

        $academicRecords = AcademicRecord::with(['subjectGrades', 'educationEnrollment'])
            ->where('beneficiary_id', $beneficiary->id)
            ->orderByDesc('id')
            ->paginate(10);

        return view('beneficiaries.academic_records.index', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'academicRecords' => $academicRecords,
        ]);
    }

    private function calculateGwa(array $subjectGrades): float
    {
        $grades = collect($subjectGrades)
            ->pluck('grade')
            ->filter(fn($grade) => $grade !== null && $grade !== '')
            ->map(fn($grade) => (float) $grade);

        return round($grades->avg() ?? 0, 2);
    }

    private function resolveGwa(array $subjectGrades, mixed $manualGwa, bool $autoCalculate = true): float
    {
        if ($autoCalculate && !empty($subjectGrades)) {
            return $this->calculateGwa($subjectGrades);
        }

        return round((float) $manualGwa, 2);
    }

    private function subjectGradeRows(array $subjectGrades): array
    {
        return collect($subjectGrades)
            ->map(function (array $subjectGrade) {
                return [
                    'subject_name' => trim((string) ($subjectGrade['subject_name'] ?? '')),
                    'grade' => (float) ($subjectGrade['grade'] ?? 0),
                ];
            })
            ->filter(fn(array $subjectGrade) => $subjectGrade['subject_name'] !== '')
            ->values()
            ->all();
    }

    public function create(Request $request, Beneficiary $beneficiary): View
    {
        $beneficiary->load('educationEnrollments');
        $selectedEnrollment = $this->enrollmentContext($beneficiary, $request->integer('enrollment_id'))
            ?? $beneficiary->educationEnrollments->sortByDesc('academic_year_start_date')->first();

        return view('beneficiaries.academic_records.create', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'enrollments' => $beneficiary->educationEnrollments->sortByDesc('academic_year_start_date')->values(),
            'selectedEnrollment' => $selectedEnrollment,
        ]);
    }

    public function store(Request $request, Beneficiary $beneficiary): RedirectResponse
    {
        if ($request->has('records')) {
            $validated = $request->validate([
                'records' => ['required', 'array', 'min:1', 'max:20'],
                'records.*.term' => ['required', 'string'],
                'records.*.enrollment_id' => ['required', 'integer', 'exists:education_enrollments,id'],
                'records.*.subject_grades' => ['nullable', 'array'],
                'records.*.subject_grades.*.subject_name' => ['required', 'string', 'max:255'],
                'records.*.subject_grades.*.grade' => ['required', 'numeric', 'min:0', 'max:100'],
                'records.*.manual_gwa' => ['nullable', 'numeric', 'min:0', 'max:100'],
                'records.*.school_attendance' => ['required', 'numeric', 'min:0', 'max:100'],
                'records.*.auto_calculate_gwa' => ['nullable', 'boolean'],
                'document_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf', 'max:5120'],
                'switch_beneficiary_id' => ['nullable', 'integer', 'exists:beneficiaries,id'],
            ]);

            $records = $validated['records'];
            $count = count($records);

            if ($count > 1 && $request->hasFile('document_file')) {
                throw ValidationException::withMessages([
                    'document_file' => 'Documents only allowed when creating a single record. Add documents via Edit after bulk creation.',
                ]);
            }

            // Custom check: each record needs subject_grades or manual_gwa
            foreach ($records as $idx => $row) {
                $sg = $this->subjectGradeRows($row['subject_grades'] ?? []);
                if (empty($sg) && (!isset($row['manual_gwa']) || $row['manual_gwa'] === '' || $row['manual_gwa'] === null)) {
                    throw ValidationException::withMessages([
                        "records.$idx.manual_gwa" => 'GWA is required when no subject grades are provided.',
                    ]);
                }
            }

            // Intra-batch duplicate (enrollment_id, term) check
            $seen = [];
            foreach ($records as $idx => $row) {
                $key = $row['enrollment_id'] . '|' . $row['term'];
                if (isset($seen[$key])) {
                    throw ValidationException::withMessages([
                        "records.$idx.term" => 'Duplicate term for the same enrollment within this batch (also at row '.($seen[$key]+1).').',
                    ]);
                }
                $seen[$key] = $idx;
            }

            $beneficiary->load('educationEnrollments');

            $created = [];

            DB::transaction(function () use ($beneficiary, $records, $request, &$created) {
                foreach ($records as $row) {
                    $subjectGrades = $this->subjectGradeRows($row['subject_grades'] ?? []);
                    $autoCalculate = (bool) ($row['auto_calculate_gwa'] ?? true);
                    $gwa = $this->resolveGwa($subjectGrades, $row['manual_gwa'] ?? null, $autoCalculate);
                    $selectedEnrollment = $this->enrollmentContext($beneficiary, (int) $row['enrollment_id']);

                    if (!$selectedEnrollment) {
                        throw ValidationException::withMessages([
                            'records' => 'Selected enrollment is invalid for this beneficiary.',
                        ]);
                    }

                    if (AcademicRecord::where('education_enrollment_id', $selectedEnrollment->id)
                        ->where('term', $row['term'])
                        ->exists()
                    ) {
                        throw ValidationException::withMessages([
                            'records' => 'An academic record for term "'.$row['term'].'" already exists for the selected enrollment.',
                        ]);
                    }

                    $record = AcademicRecord::create([
                        'beneficiary_id' => $beneficiary->id,
                        'education_enrollment_id' => $selectedEnrollment->id,
                        'term' => $row['term'],
                        'gwa' => $gwa,
                        'school_attendance' => $row['school_attendance'],
                        'created_by' => Auth::user()?->staff?->id,
                    ]);

                    if (!empty($subjectGrades)) {
                        $record->subjectGrades()->createMany($subjectGrades);
                    }

                    $created[] = $record;
                }

                if (count($created) === 1 && $request->hasFile('document_file')) {
                    $this->fileUploadService->uploadFile(
                        file: $request->file('document_file'),
                        beneficiary_id: $beneficiary->id,
                        source_module: 'academic_record',
                        source_record_id: $created[0]->id,
                        uploaded_by: Auth::id()
                    );
                }
            });

            if ($request->filled('switch_beneficiary_id')) {
                $target = Beneficiary::with('user')->find((int) $request->input('switch_beneficiary_id'));
                if ($target && $this->canSwitchTo($target)) {
                    $msg = ($count === 1 ? 'Academic record added.' : $count . ' academic records added.') . ' — now creating for ' . $target->display_name;
                    return redirect("/beneficiaries/{$target->id}/academic-records/create")->with('status', $msg);
                }
            }

            return redirect("/beneficiaries/{$beneficiary->id}/academic-records")->with('status', $count === 1 ? 'Academic record added.' : $count . ' academic records added.');
        }

        $validated = $request->validate([
            'term' => ['required', 'string'],
            'enrollment_id' => ['required', 'integer', 'exists:education_enrollments,id'],
            'subject_grades' => ['nullable', 'array'],
            'subject_grades.*.subject_name' => ['required', 'string', 'max:255'],
            'subject_grades.*.grade' => ['required', 'numeric', 'min:0', 'max:100'],
            'manual_gwa' => ['nullable', 'numeric', 'min:0', 'max:100', 'required_without:subject_grades'],
            'school_attendance' => ['required', 'numeric', 'min:0', 'max:100'],
            'auto_calculate_gwa' => ['nullable', 'boolean'],
            'document_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf', 'max:5120'],
            'switch_beneficiary_id' => ['nullable', 'integer', 'exists:beneficiaries,id'],
        ]);

        $subjectGrades = $this->subjectGradeRows($validated['subject_grades'] ?? []);
        $autoCalculate = (bool) ($validated['auto_calculate_gwa'] ?? true);
        $gwa = $this->resolveGwa($subjectGrades, $validated['manual_gwa'] ?? null, $autoCalculate);
        $selectedEnrollment = $this->enrollmentContext($beneficiary, (int) $validated['enrollment_id']);

        if (!$selectedEnrollment) {
            abort(422, 'Selected enrollment is invalid for this beneficiary.');
        }

        if (AcademicRecord::where('education_enrollment_id', $selectedEnrollment->id)
            ->where('term', $validated['term'])
            ->exists()
        ) {
            throw ValidationException::withMessages([
                'term' => 'An academic record for this term already exists for this enrollment.',
            ]);
        }

        $record = DB::transaction(function () use ($beneficiary, $validated, $subjectGrades, $gwa, $request, $selectedEnrollment) {
            $record = AcademicRecord::create([
                'beneficiary_id' => $beneficiary->id,
                'education_enrollment_id' => $selectedEnrollment->id,
                'term' => $validated['term'],
                'gwa' => $gwa,
                'school_attendance' => $validated['school_attendance'],
                'created_by' => Auth::user()?->staff?->id,
            ]);

            if (!empty($subjectGrades)) {
                $record->subjectGrades()->createMany($subjectGrades);
            }

            if ($request->hasFile('document_file')) {
                $this->fileUploadService->uploadFile(
                    file: $request->file('document_file'),
                    beneficiary_id: $beneficiary->id,
                    source_module: 'academic_record',
                    source_record_id: $record->id,
                    uploaded_by: Auth::id()
                );
            }

            return $record;
        });

        if ($request->filled('switch_beneficiary_id')) {
            $target = Beneficiary::with('user')->find((int) $request->input('switch_beneficiary_id'));
            if ($target && $this->canSwitchTo($target)) {
                return redirect("/beneficiaries/{$target->id}/academic-records/create")->with('status', 'Academic record added — now creating for ' . $target->display_name);
            }
        }

        return redirect("/beneficiaries/{$beneficiary->id}/academic-records")->with('status', 'Academic record added.');
    }

    public function show(Beneficiary $beneficiary, AcademicRecord $academicRecord): View
    {
        abort_if($academicRecord->beneficiary_id !== $beneficiary->id, 404);

        $record = $academicRecord->load(['subjectGrades', 'educationEnrollment']);

        return view('beneficiaries.academic_records.show', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'record' => $record,
        ]);
    }

    public function edit(Request $request, Beneficiary $beneficiary, AcademicRecord $academicRecord): View
    {
        abort_if($academicRecord->beneficiary_id !== $beneficiary->id, 404);

        $record = $academicRecord->load('subjectGrades');

        $beneficiary->load('educationEnrollments');
        $selectedEnrollment = $this->enrollmentContext($beneficiary, $request->integer('enrollment_id')) ?? $record->educationEnrollment;

        return view('beneficiaries.academic_records.edit', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'record' => $record,
            'enrollments' => $beneficiary->educationEnrollments->sortByDesc('academic_year_start_date')->values(),
            'selectedEnrollment' => $selectedEnrollment,
        ]);
    }

    public function update(Request $request, Beneficiary $beneficiary, AcademicRecord $academicRecord): RedirectResponse
    {
        abort_if($academicRecord->beneficiary_id !== $beneficiary->id, 404);

        $validated = $request->validate([
            'term' => ['required', 'string'],
            'enrollment_id' => ['required', 'integer', 'exists:education_enrollments,id'],
            'subject_grades' => ['nullable', 'array'],
            'subject_grades.*.subject_name' => ['required', 'string', 'max:255'],
            'subject_grades.*.grade' => ['required', 'numeric', 'min:0', 'max:100'],
            'manual_gwa' => ['nullable', 'numeric', 'min:0', 'max:100', 'required_without:subject_grades'],
            'school_attendance' => ['required', 'numeric', 'min:0', 'max:100'],
            'auto_calculate_gwa' => ['nullable', 'boolean'],
        ]);

        $subjectGrades = $this->subjectGradeRows($validated['subject_grades'] ?? []);
        $autoCalculate = (bool) ($validated['auto_calculate_gwa'] ?? true);
        $gwa = $this->resolveGwa($subjectGrades, $validated['manual_gwa'] ?? null, $autoCalculate);
        $selectedEnrollment = $this->enrollmentContext($beneficiary, (int) $validated['enrollment_id']);

        if (!$selectedEnrollment) {
            abort(422, 'Selected enrollment is invalid for this beneficiary.');
        }

        if (AcademicRecord::where('education_enrollment_id', $selectedEnrollment->id)
            ->where('term', $validated['term'])
            ->where('id', '!=', $academicRecord->id)
            ->exists()
        ) {
            throw ValidationException::withMessages([
                'term' => 'An academic record for this term already exists for this enrollment.',
            ]);
        }

        DB::transaction(function () use ($academicRecord, $validated, $subjectGrades, $gwa, $selectedEnrollment) {
            $academicRecord->update([
                'education_enrollment_id' => $selectedEnrollment->id,
                'term' => $validated['term'],
                'gwa' => $gwa,
                'school_attendance' => $validated['school_attendance'],
                'updated_by' => Auth::user()?->staff?->id,
            ]);

            $academicRecord->subjectGrades()->delete();

            if (!empty($subjectGrades)) {
                $academicRecord->subjectGrades()->createMany($subjectGrades);
            }
        });

        return redirect("/beneficiaries/{$beneficiary->id}/academic-records")->with('status', 'Academic record updated.');
    }

    public function destroy(Beneficiary $beneficiary, AcademicRecord $academicRecord): RedirectResponse
    {
        abort_if($academicRecord->beneficiary_id !== $beneficiary->id, 404);

        $academicRecord->delete();

        return redirect("/beneficiaries/{$beneficiary->id}/academic-records")->with('status', 'Academic record moved to archive. Will be automatically deleted after 90 days.');
    }

    public function bulkDestroy(Request $request, Beneficiary $beneficiary): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['integer', 'distinct', 'exists:academic_records,id'],
        ]);

        $ids = $validated['ids'];

        DB::transaction(function () use ($ids, $beneficiary): void {
            $records = AcademicRecord::whereIn('id', $ids)->get();
            foreach ($records as $record) {
                abort_if($record->beneficiary_id !== $beneficiary->id, 404);
                $record->delete();
            }
        });

        $count = count($ids);

        return back()->with('status', $count . ' academic records moved to archive. Will be automatically deleted after 90 days.');
    }

    private function canSwitchTo(Beneficiary $target): bool
    {
        $user = Auth::user();
        $staff = $user?->staff;
        if (!$staff) return false;
        if ($staff->hasAnyRole(['administrator', 'executive_director'])) return true;
        $program = $staff->staffProgram();
        if (!$program) return false;
        return $target->activePrograms()->where('programs.id', $program->id)->exists()
            || $target->programs()->where('programs.id', $program->id)->exists();
    }
}
