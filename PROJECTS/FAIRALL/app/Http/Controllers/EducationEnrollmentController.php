<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\EducationEnrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EducationEnrollmentController extends Controller
{
    public function index(Beneficiary $beneficiary): View
    {
        $enrollments = EducationEnrollment::where('beneficiary_id', $beneficiary->id)
            ->orderByDesc('academic_year_start_date')
            ->paginate(10);

        return view('beneficiaries.education_enrollments.index', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'enrollments' => $enrollments,
        ]);
    }

    public function create(Beneficiary $beneficiary): View
    {
        return view('beneficiaries.education_enrollments.create', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
        ]);
    }

    public function store(Request $request, Beneficiary $beneficiary): RedirectResponse
    {
        // Bulk mode: enrollments[0][...]
        if ($request->has('enrollments')) {
            $validated = $request->validate([
                'enrollments' => ['required', 'array', 'min:1', 'max:20'],
                'enrollments.*.school_name' => ['required', 'string', 'max:255'],
                'enrollments.*.grade_level' => ['required', Rule::in(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'])],
                'enrollments.*.academic_year' => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
                'enrollments.*.enrollment_status' => ['required', Rule::in(['active', 'completed', 'transferred', 'dropped', 'withdrawn', 'repeated'])],
                'switch_beneficiary_id' => ['nullable', 'integer', 'exists:beneficiaries,id'],
            ]);

            $count = count($validated['enrollments']);

            DB::transaction(function () use ($validated, $beneficiary): void {
                foreach ($validated['enrollments'] as $row) {
                    [$start, $end] = array_pad(explode('-', $row['academic_year'], 2), 2, now()->year);

                    EducationEnrollment::create([
                        'beneficiary_id' => $beneficiary->id,
                        'school_name' => $row['school_name'],
                        'grade_level' => $row['grade_level'],
                        'education_level' => $this->educationLevelForGradeLevel($row['grade_level']),
                        'academic_year_start_date' => trim($start) . '-01-01',
                        'academic_year_end_date' => trim($end) . '-12-31',
                        'enrollment_status' => $row['enrollment_status'],
                        'created_by' => Auth::user()?->staff?->id,
                    ]);
                }
            });

            if ($request->filled('switch_beneficiary_id')) {
                $target = Beneficiary::with('user')->find((int) $request->input('switch_beneficiary_id'));
                if ($target && $this->canSwitchTo($target)) {
                    $msg = ($count === 1 ? 'Enrollment created' : $count . ' enrollments created') . ' — now creating for ' . $target->display_name;
                    return redirect()->route('beneficiaries.enrollments.create', ['beneficiary' => $target->id])->with('status', $msg);
                }
            }

            return redirect()->route('beneficiaries.enrollments.index', ['beneficiary' => $beneficiary->id])
                ->with('status', $count === 1 ? 'Enrollment created' : $count . ' enrollments created');
        }

        $validated = $request->validate([
            'school_name' => ['required', 'string'],
            'grade_level' => ['required', Rule::in(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'])],
            'academic_year' => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
            'enrollment_status' => ['required', Rule::in(['active', 'completed', 'transferred', 'dropped', 'withdrawn', 'repeated'])],
            'switch_beneficiary_id' => ['nullable', 'integer', 'exists:beneficiaries,id'],
        ]);

        [$start, $end] = array_pad(explode('-', $validated['academic_year'], 2), 2, now()->year);

        $enrollment = EducationEnrollment::create([
            'beneficiary_id' => $beneficiary->id,
            'school_name' => $validated['school_name'],
            'grade_level' => $validated['grade_level'],
            'education_level' => $this->educationLevelForGradeLevel($validated['grade_level']),
            'academic_year_start_date' => trim($start) . '-01-01',
            'academic_year_end_date' => trim($end) . '-12-31',
            'enrollment_status' => $validated['enrollment_status'],
            'created_by' => Auth::user()?->staff?->id,
        ]);

        if ($request->filled('switch_beneficiary_id')) {
            $target = Beneficiary::with('user')->find((int) $request->input('switch_beneficiary_id'));
            if ($target && $this->canSwitchTo($target)) {
                return redirect()->route('beneficiaries.enrollments.create', ['beneficiary' => $target->id])->with('status', 'Enrollment created — now creating for ' . $target->display_name);
            }
        }

        return redirect()->route('beneficiaries.enrollments.index', ['beneficiary' => $beneficiary->id])
            ->with('status', 'Enrollment created');
    }

    public function show(Beneficiary $beneficiary, EducationEnrollment $enrollment): View
    {
        $enrollment->load(['academicRecords.subjectGrades']);

        return view('beneficiaries.education_enrollments.show', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'enrollment' => $enrollment,
            'academicRecords' => $enrollment->academicRecords->sortByDesc('created_at')->values(),
        ]);
    }

    public function edit(Beneficiary $beneficiary, EducationEnrollment $enrollment): View
    {
        return view('beneficiaries.education_enrollments.edit', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'enrollment' => $enrollment,
        ]);
    }

    public function update(Request $request, Beneficiary $beneficiary, EducationEnrollment $enrollment): RedirectResponse
    {
        $validated = $request->validate([
            'school_name' => ['required', 'string'],
            'grade_level' => ['required', Rule::in(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'])],
            'academic_year' => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
            'enrollment_status' => ['required', Rule::in(['active', 'completed', 'transferred', 'dropped', 'withdrawn', 'repeated'])],
        ]);

        [$start, $end] = array_pad(explode('-', $validated['academic_year'], 2), 2, now()->year);

        $enrollment->update([
            'school_name' => $validated['school_name'],
            'grade_level' => $validated['grade_level'],
            'education_level' => $this->educationLevelForGradeLevel($validated['grade_level']),
            'academic_year_start_date' => trim($start) . '-01-01',
            'academic_year_end_date' => trim($end) . '-12-31',
            'enrollment_status' => $validated['enrollment_status'],
            'updated_by' => Auth::user()?->staff?->id,
        ]);

        return redirect()->route('beneficiaries.enrollments.show', ['beneficiary' => $beneficiary->id, 'enrollment' => $enrollment->id])
            ->with('status', 'Enrollment updated');
    }

    public function destroy(Beneficiary $beneficiary, EducationEnrollment $enrollment): RedirectResponse
    {
        $enrollment->delete();

        return redirect()->route('beneficiaries.enrollments.index', ['beneficiary' => $beneficiary->id])->with('status', 'Education enrollment moved to archive. Will be automatically deleted after 90 days.');
    }

    public function bulkDestroy(Request $request, Beneficiary $beneficiary): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['integer', 'distinct', 'exists:education_enrollments,id'],
        ]);

        $ids = $validated['ids'];

        DB::transaction(function () use ($ids, $beneficiary): void {
            $enrollments = EducationEnrollment::whereIn('id', $ids)->get();
            foreach ($enrollments as $enrollment) {
                abort_if($enrollment->beneficiary_id !== $beneficiary->id, 404);
                $enrollment->delete();
            }
        });

        $count = count($ids);

        return back()->with('status', $count . ' education enrollments moved to archive. Will be automatically deleted after 90 days.');
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

    private function educationLevelForGradeLevel(string $gradeLevel): string
    {
        return match (trim(strtolower($gradeLevel))) {
            '1','2','3','4','5','6' => 'elementary',
            '7','8','9','10' => 'high_school',
            '11','12' => 'shs',
            default => 'college',
        };
    }
}
