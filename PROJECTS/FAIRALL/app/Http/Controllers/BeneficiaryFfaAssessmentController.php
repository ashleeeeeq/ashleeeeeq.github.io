<?php

namespace App\Http\Controllers;

use App\Models\AssessmentCategory;
use App\Models\Beneficiary;
use App\Models\FfaAssessmentRecord;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BeneficiaryFfaAssessmentController extends Controller
{
    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    private function canViewSensitiveCategory(): bool
    {
        $staff = Auth::user()?->staff;

        return $staff && $staff->hasAnyPosition(['Social Worker', 'Researcher', 'System Admin']);
    }

    private function getSocioEmotionalCategoryId(): ?int
    {
        return AssessmentCategory::whereRaw('LOWER(assessment_name) = ?', ['socio-emotional'])
            ->value('id');
    }

    public function index(Beneficiary $beneficiary): View
    {
        $query = $beneficiary->ffaAssessmentRecords()->with('assessmentCategory');

        if (!$this->canViewSensitiveCategory()) {
            $socioEmotionalCategoryId = $this->getSocioEmotionalCategoryId();
            if ($socioEmotionalCategoryId) {
                $query->where('assessment_category_id', '!=', $socioEmotionalCategoryId);
            }
        }

        $ffaRecords = $query->orderByDesc('date')->orderByDesc('id')->paginate(10);

        return view('beneficiaries.ffa_assessments.index', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'ffaRecords' => $ffaRecords,
        ]);
    }

    public function create(Beneficiary $beneficiary): View
    {
        $categories = AssessmentCategory::pluck('assessment_name', 'id');

        if (!$this->canViewSensitiveCategory()) {
            $categories = $categories->reject(
                fn($name) => strtolower($name) === 'socio-emotional'
            );
        }

        return view('beneficiaries.ffa_assessments.create', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request, Beneficiary $beneficiary): RedirectResponse
    {
        if ($request->has('assessments')) {
            $validated = $request->validate([
                'assessments' => ['required', 'array', 'min:1', 'max:20'],
                'assessments.*.assessment_category_id' => ['required', 'integer', 'exists:assessment_categories,id'],
                'assessments.*.name' => ['nullable', 'string', 'max:255'],
                'assessments.*.score' => ['required', 'numeric'],
                'assessments.*.max_score' => ['required', 'numeric'],
                'assessments.*.date' => ['required', 'date'],
                'assessments.*.remarks' => ['nullable', 'string', 'max:1000'],
                'document_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf', 'max:5120'],
                'switch_beneficiary_id' => ['nullable', 'integer', 'exists:beneficiaries,id'],
            ]);

            $count = count($validated['assessments']);

            if ($count > 1 && $request->hasFile('document_file')) {
                return back()->withErrors(['document_file' => 'Documents only allowed when creating a single record. Add documents via Edit after bulk creation.'])->withInput();
            }

            $socioId = $this->getSocioEmotionalCategoryId();
            $canViewSensitive = $this->canViewSensitiveCategory();

            if (!$canViewSensitive && $socioId) {
                foreach ($validated['assessments'] as $idx => $row) {
                    if ((int) ($row['assessment_category_id'] ?? 0) === $socioId) {
                        abort(403, 'You are not authorized to create Socio-Emotional assessments.');
                    }
                }
            }

            $created = [];

            DB::transaction(function () use ($beneficiary, $validated, $request, $count, &$created) {
                foreach ($validated['assessments'] as $row) {
                    $record = $beneficiary->ffaAssessmentRecords()->create([
                        'assessment_category_id' => $row['assessment_category_id'],
                        'name' => $row['name'] ?? null,
                        'score' => $row['score'],
                        'max_score' => $row['max_score'],
                        'date' => $row['date'],
                        'remarks' => $row['remarks'] ?? null,
                        'created_by' => Auth::user()?->staff?->id,
                    ]);
                    $created[] = $record;
                }

                if ($count === 1 && $request->hasFile('document_file') && isset($created[0])) {
                    $this->fileUploadService->uploadFile(
                        file: $request->file('document_file'),
                        beneficiary_id: $beneficiary->id,
                        source_module: 'ffa_assessment',
                        source_record_id: $created[0]->id,
                        uploaded_by: Auth::id()
                    );
                }
            });

            if ($request->filled('switch_beneficiary_id')) {
                $target = Beneficiary::with('user')->find((int) $request->input('switch_beneficiary_id'));
                if ($target && $this->canSwitchTo($target)) {
                    $msg = ($count === 1 ? 'FFA assessment added.' : $count . ' FFA assessments added.') . ' — now creating for ' . $target->display_name;
                    return redirect("/beneficiaries/{$target->id}/ffa-assessments/create")->with('status', $msg);
                }
            }

            return redirect("/beneficiaries/{$beneficiary->id}/ffa-assessments")->with('status', $count === 1 ? 'FFA assessment added.' : $count . ' FFA assessments added.');
        }

        $validated = $request->validate([
            'assessment_category_id' => ['required', 'integer', 'exists:assessment_categories,id'],
            'name' => ['sometimes', 'string'],
            'score' => ['required', 'numeric'],
            'max_score' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            'document_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf', 'max:5120'],
            'switch_beneficiary_id' => ['nullable', 'integer', 'exists:beneficiaries,id'],
        ]);

        if (!$this->canViewSensitiveCategory()) {
            $socioEmotionalCategoryId = $this->getSocioEmotionalCategoryId();
            if ($socioEmotionalCategoryId && (int) $validated['assessment_category_id'] === $socioEmotionalCategoryId) {
                abort(403, 'You are not authorized to create Socio-Emotional assessments.');
            }
        }

        $record = DB::transaction(function () use ($beneficiary, $validated, $request) {
            $record = $beneficiary->ffaAssessmentRecords()->create([
                'assessment_category_id' => $validated['assessment_category_id'],
                'name' => $validated['name'] ?? null,
                'score' => $validated['score'],
                'max_score' => $validated['max_score'],
                'date' => $validated['date'],
                'created_by' => Auth::user()?->staff?->id,
            ]);

            if ($request->hasFile('document_file')) {
                $this->fileUploadService->uploadFile(
                    file: $request->file('document_file'),
                    beneficiary_id: $beneficiary->id,
                    source_module: 'ffa_assessment',
                    source_record_id: $record->id,
                    uploaded_by: Auth::id()
                );
            }

            return $record;
        });

        if ($request->filled('switch_beneficiary_id')) {
            $target = Beneficiary::with('user')->find((int) $request->input('switch_beneficiary_id'));
            if ($target && $this->canSwitchTo($target)) {
                return redirect("/beneficiaries/{$target->id}/ffa-assessments/create")->with('status', 'FFA assessment added — now creating for ' . $target->display_name);
            }
        }

        return redirect("/beneficiaries/{$beneficiary->id}/ffa-assessments")->with('status', 'FFA assessment added.');
    }

    public function edit(Beneficiary $beneficiary, FfaAssessmentRecord $ffaAssessment): View
    {
        $categories = AssessmentCategory::pluck('assessment_name', 'id');

        if (!$this->canViewSensitiveCategory()) {
            $categories = $categories->reject(
                fn($name) => strtolower($name) === 'socio-emotional'
            );
        }

        return view('beneficiaries.ffa_assessments.edit', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'record' => $ffaAssessment,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Beneficiary $beneficiary, FfaAssessmentRecord $ffaAssessment): RedirectResponse
    {
        $validated = $request->validate([
            'assessment_category_id' => ['required', 'integer', 'exists:assessment_categories,id'],
            'name' => ['sometimes', 'string'],
            'score' => ['required', 'numeric'],
            'max_score' => ['required', 'numeric'],
            'date' => ['required', 'date'],
        ]);

        if (!$this->canViewSensitiveCategory()) {
            $socioEmotionalCategoryId = $this->getSocioEmotionalCategoryId();
            if ($socioEmotionalCategoryId && (int) $validated['assessment_category_id'] === $socioEmotionalCategoryId) {
                abort(403, 'You are not authorized to update Socio-Emotional assessments.');
            }
        }

        $ffaAssessment->update(array_merge($validated, ['updated_by' => Auth::user()?->staff?->id]));

        return redirect("/beneficiaries/{$beneficiary->id}/ffa-assessments")->with('status', 'FFA assessment updated.');
    }

    public function destroy(Beneficiary $beneficiary, FfaAssessmentRecord $ffaAssessment): RedirectResponse
    {
        $ffaAssessment->delete();

        return redirect("/beneficiaries/{$beneficiary->id}/ffa-assessments")->with('status', 'FFA assessment moved to archive. Will be automatically deleted after 90 days.');
    }

    public function bulkDestroy(Request $request, Beneficiary $beneficiary): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['integer', 'distinct', 'exists:ffa_assessment_records,id'],
        ]);

        $ids = $validated['ids'];

        DB::transaction(function () use ($ids, $beneficiary): void {
            $records = FfaAssessmentRecord::whereIn('id', $ids)->get();
            foreach ($records as $record) {
                abort_if($record->beneficiary_id !== $beneficiary->id, 404);
                $record->delete();
            }
        });

        $count = count($ids);

        return back()->with('status', $count . ' FFA assessments moved to archive. Will be automatically deleted after 90 days.');
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
