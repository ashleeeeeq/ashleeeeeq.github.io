<?php

namespace Database\Seeders;

use App\Models\AcademicRecord;
use App\Models\Beneficiary;
use App\Models\BeneficiaryGuardian;
use App\Models\BeneficiaryProgramMembership;
use App\Models\BeneficiaryStatus;
use App\Models\BeneficiaryStatusType;
use App\Models\EducationEnrollment;
use App\Models\EducationIntakeSheet;
use App\Models\Program;
use App\Models\SubjectGrade;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BeneficiariesSeeder extends Seeder
{
    public function run(): void
    {
        $seed = (int) env('SEED_RANDOM', 42);
        fake()->seed($seed);

        $programIds = Program::query()->pluck('id', 'program_name')->all();
        $statusTypeIds = BeneficiaryStatusType::query()
            ->pluck('id', 'status_name')
            ->all();

        DB::transaction(function () use ($programIds, $statusTypeIds): void {
            $this->seedGroup($programIds, $statusTypeIds, 'Sports', 250);
            $this->seedGroup($programIds, $statusTypeIds, 'Education', 100);
            $this->seedGroup($programIds, $statusTypeIds, 'Mixed', 50);
        });
    }

    private function seedGroup(array $programIds, array $statusTypeIds, string $group, int $count): void
    {
        $sportProgramId = $programIds['Sports'];
        $educationProgramId = $programIds['Education'];
        // $educationGrades = array_slice(SeederSupport::gradeOrder(), 0, 12);
        $educationGrades = ['2', '5', '7', '12', '1st Year'];
        $intakeDate = Carbon::create(2020, 6, 1);

        // Build users and beneficiary rows first for bulk insert, then map IDs
        $userRows = [];
        $meta = [];
        $timestamp = now();
        $hashedPassword = Hash::make('Password123!');

        for ($index = 0; $index < $count; $index++) {
            $sex = fake()->randomElement(['male', 'female']);
            $educationGrade = in_array($group, ['Education', 'Mixed'], true)
                ? Arr::random($educationGrades)
                : null;

            $email = fake()->unique()->safeEmail();

            $userRows[] = [
                'login_id' => null,
                'email' => $email,
                'password' => $hashedPassword,
                'is_active' => true,
                'user_type' => 'beneficiary',
                'email_verified_at' => $timestamp,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];

            $meta[] = [
                'email' => $email,
                'sex' => $sex,
                'educationGrade' => $educationGrade,
                'first_name' => fake()->firstName($sex),
                'middle_name' => fake()->optional()->firstName(),
                'last_name' => fake()->lastName(),
                'birth_date' => $educationGrade
                    ? SeederSupport::birthDateForIntakeGradeLevel($educationGrade)->format('Y-m-d')
                    : fake()->dateTimeBetween('-18 years', '-8 years')->format('Y-m-d'),
                'contact_number' => '9' . fake()->numerify('#########'),
                'dial_code' => '+63',
                'form_given' => true,
            ];
        }

        // Insert users in chunks
        foreach (array_chunk($userRows, 500) as $chunk) {
            DB::table('users')->insert($chunk);
        }

        $emails = array_map(fn($m) => $m['email'], $meta);
        $userIdByEmail = DB::table('users')->whereIn('email', $emails)->pluck('id', 'email')->all();

        // Build beneficiary rows referencing inserted users
        $beneficiaryRows = [];
        foreach ($meta as $m) {
            $uid = $userIdByEmail[$m['email']] ?? null;
            if ($uid === null) {
                continue;
            }

            $beneficiaryRows[] = [
                'user_id' => $uid,
                'first_name' => $m['first_name'],
                'middle_name' => $m['middle_name'],
                'last_name' => $m['last_name'],
                'birth_date' => $m['birth_date'],
                'sex' => $m['sex'],
                'contact_number' => $m['contact_number'],
                'dial_code' => $m['dial_code'] ?? '+63',
                'form_given' => $m['form_given'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        foreach (array_chunk($beneficiaryRows, 500) as $chunk) {
            DB::table('beneficiaries')->insert($chunk);
        }

        // Map user -> beneficiary id
        $userIds = array_values($userIdByEmail);
        $beneficiaryIdByUser = DB::table('beneficiaries')->whereIn('user_id', $userIds)->pluck('id', 'user_id')->all();

        $beneficiaryIds = array_values($beneficiaryIdByUser);

        // Now run the original per-beneficiary follow-ups (guardians, memberships, education factories, statuses)
        foreach ($meta as $m) {
            $uid = $userIdByEmail[$m['email']] ?? null;
            if ($uid === null) {
                continue;
            }

            $beneficiaryId = $beneficiaryIdByUser[$uid] ?? null;
            if ($beneficiaryId === null) {
                continue;
            }

            // set login id to include beneficiary id
            DB::table('users')->where('id', $uid)->update([
                'login_id' => sprintf('BEN-%d-%d', now()->year, $beneficiaryId),
            ]);

            $address = [
                'address_line' => fake()->address(),
                'city' => 'Quezon City',
                'province' => 'Metro Manila',
                'zip' => '1234',
                'country' => 'Philippines',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];

            DB::table('addresses')->insert([
                'beneficiary_id' => $beneficiaryId,
                ...$address,
            ]);

            $addressGuardianIds = BeneficiaryGuardian::factory()->count(fake()->numberBetween(1, 2))->create([
                'beneficiary_id' => $beneficiaryId,
            ])->pluck('id');

            foreach ($addressGuardianIds as $guardianId) {
                DB::table('addresses')->insert([
                    'guardian_id' => $guardianId,
                    ...$address,
                ]);
            }

            $sportsEnrolledAt = null;
            $probationStart = $intakeDate->copy();

            if ($group === 'Sports' || $group === 'Mixed') {
                $sportsEnrolledAt = Carbon::parse(fake()->dateTimeBetween('-6 years', '-6 months'));

                BeneficiaryProgramMembership::create([
                    'beneficiary_id' => $beneficiaryId,
                    'program_id' => $sportProgramId,
                    'enrolled_at' => $sportsEnrolledAt,
                ]);
            }

            if ($group === 'Education' || $group === 'Mixed') {
                BeneficiaryProgramMembership::create([
                    'beneficiary_id' => $beneficiaryId,
                    'program_id' => $educationProgramId,
                    'enrolled_at' => $probationStart,
                ]);

                $gradeLevel = $m['educationGrade'];
                $educationLevel = SeederSupport::educationLevelForGradeLevel($gradeLevel);
                $educationIntake = EducationIntakeSheet::factory()->create([
                    'beneficiary_id' => $beneficiaryId,
                    'age' => SeederSupport::ageForIntakeGradeLevel($gradeLevel),
                    'grade_level' => $gradeLevel,
                    'school_name' => SeederSupport::schoolNameForGrade($gradeLevel),
                    'highest_education' => SeederSupport::previousEducationStage($educationLevel),
                    'created_at' => $intakeDate,
                    'updated_at' => $intakeDate,
                ]);

                $schoolName = $educationIntake->school_name;
                $academicYearStart = now()->year - 1;
                $enrollment = EducationEnrollment::factory()->create([
                    'beneficiary_id' => $beneficiaryId,
                    'school_name' => $schoolName,
                    'academic_year_start_date' => $academicYearStart . '-06-01',
                    'academic_year_end_date' => now()->year . '-03-31',
                    'education_level' => SeederSupport::educationLevelForGradeLevel($gradeLevel),
                    'grade_level' => $gradeLevel,
                    'enrollment_status' => 'completed',
                ]);

                $academicRecord = AcademicRecord::factory()->create([
                    'beneficiary_id' => $beneficiaryId,
                    'education_enrollment_id' => $enrollment->id,
                    'term' => '4th',
                    'gwa' => fake()->randomFloat(2, 80, 100),
                    'school_attendance' => fake()->randomFloat(2, 80, 100),
                ]);

                foreach ($this->subjectsForGrade($gradeLevel) as $subjectName) {
                    SubjectGrade::factory()->create([
                        'academic_record_id' => $academicRecord->id,
                        'subject_name' => $subjectName,
                        'grade' => fake()->randomFloat(2, 80, 98),
                    ]);
                }

                if (isset($statusTypeIds['Probation Scholar'])) {
                    $probationEnd = $probationStart->copy()->addYear();

                    BeneficiaryStatus::firstOrCreate([
                        'beneficiary_id' => $beneficiaryId,
                        'beneficiary_status_type_id' => $statusTypeIds['Probation Scholar'],
                        'start_date' => $probationStart->toDateString(),
                    ], [
                        'end_date' => $probationEnd->toDateString(),
                    ]);
                }

                if (isset($statusTypeIds['Full Scholar'])) {
                    $fullScholarStart = $probationStart->copy()->addYear();

                    BeneficiaryStatus::firstOrCreate([
                        'beneficiary_id' => $beneficiaryId,
                        'beneficiary_status_type_id' => $statusTypeIds['Full Scholar'],
                        'start_date' => $fullScholarStart->toDateString(),
                    ], [
                        'end_date' => null,
                    ]);
                }
            }

            if (($group === 'Sports' || $group === 'Mixed') && $sportsEnrolledAt !== null) {
                if (isset($statusTypeIds['Registered Player'])) {
                    BeneficiaryStatus::firstOrCreate([
                        'beneficiary_id' => $beneficiaryId,
                        'beneficiary_status_type_id' => $statusTypeIds['Registered Player'],
                        'start_date' => $sportsEnrolledAt->toDateString(),
                    ], [
                        'end_date' => null,
                    ]);
                }
            }
        }

        $this->addExitDates($beneficiaryIds, $group, $sportProgramId, $educationProgramId, $statusTypeIds);
    }

    private function addExitDates(array $beneficiaryIds, string $group, int $sportProgramId, int $educationProgramId, array $statusTypeIds): void
    {
        $timestamp = now();
        $now = $timestamp->copy();
        $exitCandidates = collect($beneficiaryIds)->shuffle()->take((int) floor(count($beneficiaryIds) * 0.2));

        foreach ($exitCandidates as $beneficiaryId) {
            if ($group === 'Sports' || $group === 'Mixed') {
                $membership = DB::table('beneficiary_program_memberships')
                    ->where('beneficiary_id', $beneficiaryId)
                    ->where('program_id', $sportProgramId)
                    ->first(['enrolled_at']);

                $startDate = null;
                if (isset($statusTypeIds['Registered Player'])) {
                    $startDate = DB::table('beneficiary_statuses')
                        ->where('beneficiary_id', $beneficiaryId)
                        ->where('beneficiary_status_type_id', $statusTypeIds['Registered Player'])
                        ->value('start_date');
                }

                $minDate = Carbon::parse($membership?->enrolled_at ?? '2020-01-01');
                if ($startDate) {
                    $parsed = Carbon::parse($startDate);
                    if ($parsed->gt($minDate)) {
                        $minDate = $parsed;
                    }
                }
                $minDate = $minDate->copy()->addYear();

                if ($minDate->lt($now)) {
                    $exitDate = Carbon::create(
                        fake()->numberBetween($minDate->year, $now->year),
                        fake()->numberBetween(1, 12),
                        fake()->numberBetween(1, 28)
                    );
                    if ($exitDate->gt($now)) {
                        $exitDate = $now->copy()->subMonth();
                    }
                    if ($exitDate->gt($minDate)) {
                        DB::table('beneficiary_program_memberships')
                            ->where('beneficiary_id', $beneficiaryId)
                            ->where('program_id', $sportProgramId)
                            ->update(['exited_at' => $exitDate, 'updated_at' => $timestamp]);

                        if (isset($statusTypeIds['Registered Player'])) {
                            DB::table('beneficiary_statuses')
                                ->where('beneficiary_id', $beneficiaryId)
                                ->where('beneficiary_status_type_id', $statusTypeIds['Registered Player'])
                                ->update(['end_date' => $exitDate->toDateString(), 'updated_at' => $timestamp]);
                        }
                    }
                }
            }

            if ($group === 'Education' || $group === 'Mixed') {
                $membership = DB::table('beneficiary_program_memberships')
                    ->where('beneficiary_id', $beneficiaryId)
                    ->where('program_id', $educationProgramId)
                    ->first(['enrolled_at']);

                $startDate = null;
                if (isset($statusTypeIds['Full Scholar'])) {
                    $startDate = DB::table('beneficiary_statuses')
                        ->where('beneficiary_id', $beneficiaryId)
                        ->where('beneficiary_status_type_id', $statusTypeIds['Full Scholar'])
                        ->orderByDesc('start_date')
                        ->value('start_date');
                }

                $minDate = Carbon::parse($membership?->enrolled_at ?? '2020-01-01');
                if ($startDate) {
                    $parsed = Carbon::parse($startDate);
                    if ($parsed->gt($minDate)) {
                        $minDate = $parsed;
                    }
                }
                $minDate = $minDate->copy()->addYear();

                if ($minDate->lt($now)) {
                    $exitDate = Carbon::create(
                        fake()->numberBetween($minDate->year, $now->year),
                        fake()->numberBetween(1, 12),
                        fake()->numberBetween(1, 28)
                    );
                    if ($exitDate->gt($now)) {
                        $exitDate = $now->copy()->subMonth();
                    }
                    if ($exitDate->gt($minDate)) {
                        DB::table('beneficiary_program_memberships')
                            ->where('beneficiary_id', $beneficiaryId)
                            ->where('program_id', $educationProgramId)
                            ->update(['exited_at' => $exitDate, 'updated_at' => $timestamp]);

                        if (isset($statusTypeIds['Full Scholar'])) {
                            $fullScholars = DB::table('beneficiary_statuses')
                                ->where('beneficiary_id', $beneficiaryId)
                                ->where('beneficiary_status_type_id', $statusTypeIds['Full Scholar'])
                                ->orderByDesc('start_date')
                                ->limit(1)
                                ->get();

                            foreach ($fullScholars as $fs) {
                                DB::table('beneficiary_statuses')
                                    ->where('id', $fs->id)
                                    ->update(['end_date' => $exitDate->toDateString(), 'updated_at' => $timestamp]);
                            }
                        }
                    }
                }
            }
        }
    }

    private function subjectsForGrade(string $gradeLevel): array
    {
        return in_array($gradeLevel, ['1', '2', '3', '4', '5', '6'], true)
            ? ['Filipino', 'English', 'Mathematics', 'Science', 'MAPEH']
            : ['Filipino', 'English', 'Mathematics', 'Science', 'Araling Panlipunan', 'MAPEH', 'Edukasyon sa Pagpapakatao'];
    }
}
