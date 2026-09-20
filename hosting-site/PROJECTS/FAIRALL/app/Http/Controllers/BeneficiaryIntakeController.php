<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Models\Staff;

class BeneficiaryIntakeController extends Controller
{
    public function create(Beneficiary $beneficiary): View
    {
        $beneficiary->load(['intakeSheet', 'activePrograms', 'programs']);
        $staff = Auth::user()?->staff;
        $this->authorizeEducationAccess($beneficiary, $staff);

        return view('beneficiaries.education-intake.create', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'intakeSheet' => $beneficiary->intakeSheet,
        ]);
    }

    public function store(Request $request, Beneficiary $beneficiary): RedirectResponse
    {
        $beneficiary->load(['intakeSheet', 'activePrograms', 'programs']);
        $staff = Auth::user()?->staff;
        $this->authorizeEducationAccess($beneficiary, $staff);

        $validated = $request->validate($this->intakeRules());
        $staffId = $staff?->id;

        $this->upsertIntakeSheet($beneficiary, $validated['intake'], $staffId);

        // Create program membership if not already enrolled in Education program
        $educationProgram = Program::query()->where('program_name', 'Education')->first();
        if ($educationProgram) {
            $membershipExists = DB::table('beneficiary_program_memberships')
                ->where('beneficiary_id', $beneficiary->id)
                ->where('program_id', $educationProgram->id)
                ->whereNull('exited_at')
                ->exists();

            if (!$membershipExists) {
                DB::table('beneficiary_program_memberships')->insert([
                    'beneficiary_id' => $beneficiary->id,
                    'program_id' => $educationProgram->id,
                    'enrolled_at' => now(),
                    'created_by' => $staffId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('beneficiaries.enrollment.index')
            ->with('status', 'Education intake sheet saved successfully. Beneficiary enrolled in Education program.');
    }

    public function edit(Beneficiary $beneficiary): View
    {
        $beneficiary->load(['intakeSheet', 'activePrograms', 'programs']);
        $staff = Auth::user()?->staff;
        $this->authorizeEducationAccess($beneficiary, $staff);

        return view('beneficiaries.education-intake.edit', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'intakeSheet' => $beneficiary->intakeSheet,
        ]);
    }

    public function update(Request $request, Beneficiary $beneficiary): RedirectResponse
    {
        $beneficiary->load(['intakeSheet', 'activePrograms', 'programs']);
        $staff = Auth::user()?->staff;
        $this->authorizeEducationAccess($beneficiary, $staff);

        $validated = $request->validate($this->intakeRules());
        $staffId = $staff?->id;

        $this->upsertIntakeSheet($beneficiary, $validated['intake'], $staffId);

        return redirect("/beneficiaries/{$beneficiary->id}/profile")
            ->with('status', 'Education intake sheet updated successfully.');
    }

    private function authorizeEducationAccess(Beneficiary $beneficiary, ?Staff $staff): void
    {
        $isAdminExec = $staff?->hasAnyRole(['administrator', 'executive_director']) ?? false;

        $educationProgram = Program::where('program_name', 'Education')->first();

        // only allow admin, exec, and program manager in education program
        if (!$isAdminExec && (!$staff->hasAnyRole(['program_manager']) && !$staff->matchesProgramModel($educationProgram))) {
            abort(403, 'You are not allowed to access this intake sheet.');
        }
    }

    private function intakeRules(): array
    {
        $educationLevels = ['pre-school', 'elementary', 'high_school', 'shs', 'college', 'post-grad'];
        $gradeLevels = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];

        return [
            'intake.age' => ['required', 'integer', 'min:1'],
            'intake.place_of_birth' => ['required', 'string', 'max:80'],
            'intake.number_of_siblings' => ['required', 'integer', 'min:0'],
            'intake.older_sibling_age' => ['nullable', 'integer', 'min:0'],
            'intake.younger_sibling_age' => ['nullable', 'integer', 'min:0'],
            'intake.civil_status' => ['required', 'string', 'max:50'],
            'intake.highest_education' => ['required', Rule::in($educationLevels)],
            'intake.school_name' => ['required'],
            'intake.grade_level' => ['required', Rule::in($gradeLevels)],
            'intake.other_scholarship' => ['sometimes', 'boolean'],
            'intake.scholarship_org_question' => [
                Rule::requiredIf(fn () => (bool) request('intake.other_scholarship')),
                'nullable',
                'string',
                'max:5000',
            ],
            'intake.hardworking_question' => ['required', 'string', 'max:5000'],
            'intake.dream_question' => ['required', 'string', 'max:5000'],
            'intake.scholarship_question' => ['required', 'string', 'max:5000'],
        ];
    }

    private function upsertIntakeSheet(Beneficiary $beneficiary, array $intake, ?int $staffId): void
    {
        $payload = [
            'age' => $intake['age'],
            'place_of_birth' => $intake['place_of_birth'],
            'number_of_siblings' => $intake['number_of_siblings'],
            'older_sibling_age' => $intake['older_sibling_age'] ?? null,
            'younger_sibling_age' => $intake['younger_sibling_age'] ?? null,
            'civil_status' => $intake['civil_status'],
            'highest_education' => $intake['highest_education'],
            'school_name' => $intake['school_name'],
            'grade_level' => $intake['grade_level'],
            'other_scholarship' => (bool) ($intake['other_scholarship'] ?? false),
            'scholarship_org_question' => $intake['scholarship_org_question'] ?? null,
            'hardworking_question' => $intake['hardworking_question'],
            'dream_question' => $intake['dream_question'],
            'scholarship_question' => $intake['scholarship_question'],
            'updated_by' => $staffId,
        ];

        if ($beneficiary->intakeSheet) {
            $beneficiary->intakeSheet()->update($payload);
            return;
        }

        $payload['created_by'] = $staffId;
        $beneficiary->intakeSheet()->create($payload);
    }
}

