<?php

namespace Database\Seeders;

use App\Models\AssessmentCategory;
use App\Models\Beneficiary;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EducationSeeder extends Seeder
{
    /** @var array<int, Carbon|null> */
    private array $exitDates = [];

    public function run(): void
    {
        $adminStaffId = $this->adminStaffId();
        $assessmentCategoryIds = AssessmentCategory::query()->pluck('id', 'assessment_name')->all();
        $timestamp = now();

        $this->exitDates = DB::table('beneficiary_program_memberships')
            ->join('programs', 'beneficiary_program_memberships.program_id', '=', 'programs.id')
            ->where('programs.program_name', 'Education')
            ->whereNotNull('exited_at')
            ->pluck('exited_at', 'beneficiary_id')
            ->map(fn ($date) => $date ? Carbon::parse($date) : null)
            ->all();

        Beneficiary::query()
            ->whereHas('programs', function ($query): void {
                $query->where('program_name', 'Education');
            })
            ->with('intakeSheet')
            ->chunkById(25, function ($beneficiaries) use ($adminStaffId, $assessmentCategoryIds, $timestamp): void {
                foreach ($beneficiaries as $beneficiary) {
                    $this->seedHistoryForBeneficiary($beneficiary, $adminStaffId, $assessmentCategoryIds, $timestamp);
                }
            });
    }

    private function seedHistoryForBeneficiary(Beneficiary $beneficiary, int $adminStaffId, array $assessmentCategoryIds, Carbon $timestamp): void
    {
        $startingGrade = $beneficiary->intakeSheet?->grade_level;

        if ($startingGrade === null) {
            return;
        }

        DB::table('ffa_assessment_records')->where('beneficiary_id', $beneficiary->id)->delete();
        DB::table('academic_records')->where('beneficiary_id', $beneficiary->id)->delete();
        DB::table('education_enrollments')->where('beneficiary_id', $beneficiary->id)->delete();

        $gradeLevel = $startingGrade;
        $currentAcademicYear = now()->year - 1; // so only until 2025-2026 are created for now
        $enrollmentRows = [];
        $enrollmentContexts = [];
        $assessmentRows = [];

        $exitDate = $this->exitDates[$beneficiary->id] ?? null;

        for ($academicYear = 2020; $academicYear <= $currentAcademicYear && $gradeLevel !== null; $academicYear++) {
            if ($exitDate !== null && Carbon::create($academicYear, 6, 1)->gt($exitDate)) {
                break;
            }

            $educationLevel = SeederSupport::educationLevelForGradeLevel($gradeLevel);
            $schoolName = SeederSupport::schoolNameForGrade($gradeLevel);
            $termLabels = SeederSupport::termsForGradeLevel($gradeLevel);
            $academicYearStartDate = Carbon::create($academicYear, 6, 1)->toDateString();
            $academicYearEndDate = Carbon::create($academicYear + 1, 3, 31)->toDateString();
            $subjectsForGrade = SeederSupport::subjectsForGrade($gradeLevel);

            $enrollmentRows[] = [
                'beneficiary_id' => $beneficiary->id,
                'school_name' => $schoolName,
                'academic_year_start_date' => $academicYearStartDate,
                'academic_year_end_date' => $academicYearEndDate,
                'education_level' => $educationLevel,
                'grade_level' => $gradeLevel,
                'enrollment_status' => 'completed',
                'created_by' => $adminStaffId,
                'updated_by' => $adminStaffId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];

            $enrollmentContexts[] = [
                'academic_year' => $academicYear,
                'grade_level' => $gradeLevel,
                'term_labels' => $termLabels,
                'subjects' => $subjectsForGrade,
            ];

            foreach ($assessmentCategoryIds as $assessmentName => $assessmentCategoryId) {
                $assessmentRows[] = [
                    'beneficiary_id' => $beneficiary->id,
                    'assessment_category_id' => $assessmentCategoryId,
                    'name' => $assessmentName . ' - Grade ' . $gradeLevel,
                    'score' => rand(85, 100),
                    'max_score' => 100,
                    'remarks' => 'Seeded yearly assessment',
                    'date' => Carbon::create($academicYear + 1, 3, 15)->toDateString(),
                    'created_by' => $adminStaffId,
                    'updated_by' => $adminStaffId,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }

            $gradeLevel = SeederSupport::nextGradeLevel($gradeLevel);
        }

        foreach (array_chunk($enrollmentRows, 500) as $chunk) {
            DB::table('education_enrollments')->insert($chunk);
        }

        $insertedEnrollments = DB::table('education_enrollments')
            ->where('beneficiary_id', $beneficiary->id)
            ->orderBy('academic_year_start_date')
            ->orderBy('id')
            ->get(['id']);

        $academicRecordRows = [];
        $academicRecordContexts = [];

        foreach ($enrollmentContexts as $index => $context) {
            $enrollmentId = $insertedEnrollments[$index]->id ?? null;

            if ($enrollmentId === null) {
                continue;
            }

            foreach ($context['term_labels'] as $termIndex => $termLabel) {
                $academicRecordRows[] = [
                    'beneficiary_id' => $beneficiary->id,
                    'education_enrollment_id' => $enrollmentId,
                    'term' => $termLabel,
                    'gwa' => rand(85, 100),
                    'school_attendance' => rand(85, 100),
                    'created_by' => $adminStaffId,
                    'updated_by' => $adminStaffId,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];

                $academicRecordContexts[] = [
                    'academic_year' => $context['academic_year'],
                    'term_index' => $termIndex,
                    'subjects' => $context['subjects'],
                ];
            }
        }

        foreach (array_chunk($academicRecordRows, 500) as $chunk) {
            DB::table('academic_records')->insert($chunk);
        }

        $insertedAcademicRecords = DB::table('academic_records')
            ->where('beneficiary_id', $beneficiary->id)
            ->orderBy('education_enrollment_id')
            ->orderBy('id')
            ->get(['id']);

        $subjectGradeRows = [];

        foreach ($insertedAcademicRecords as $index => $academicRecord) {
            $context = $academicRecordContexts[$index] ?? null;

            if ($context === null) {
                continue;
            }

            foreach ($context['subjects'] as $subject) {
                $subjectGradeRows[] = [
                    'academic_record_id' => $academicRecord->id,
                    'subject_name' => $subject['name'],
                    'grade' => rand(85, 100),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }
        }

        foreach (array_chunk($subjectGradeRows, 500) as $chunk) {
            DB::table('subject_grades')->insert($chunk);
        }

        foreach (array_chunk($assessmentRows, 500) as $chunk) {
            DB::table('ffa_assessment_records')->insert($chunk);
        }
    }

    private function adminStaffId(): int
    {
        return SeederSupport::CREATOR_STAFF_ID;
    }
}
