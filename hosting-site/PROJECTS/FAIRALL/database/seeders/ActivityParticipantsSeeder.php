<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Beneficiary;
use App\Models\BeneficiaryStatusType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityParticipantsSeeder extends Seeder
{
    /** @var array<int, object> */
    private array $probationStarts = [];

    /** @var array<int, object> */
    private array $playerStarts = [];

    /** @var array<int, Carbon|null> */
    private array $sportsExits = [];

    /** @var array<int, Carbon|null> */
    private array $educationExits = [];

    public function run(): void
    {
        $this->loadProgramStartDates();

        $activities = Activity::query()->with(['activityType', 'sportType', 'program'])->orderBy('id')->get();
        $programIds = $activities->pluck('program_id')->filter()->unique()->values()->all();
        $beneficiaryPools = [];

        foreach ($programIds as $programId) {
            $beneficiaryPools[$programId] = Beneficiary::query()
                ->whereHas('programs', function ($query) use ($programId): void {
                    $query->where('programs.id', $programId);
                })
                ->with('intakeSheet')
                ->get();
        }

        $this->loadExitDates();

        foreach ($activities as $activity) {
            if ($activity->program?->program_name === 'Sports' || $activity->sportType !== null) {
                $this->seedSportsParticipants($activity, (int) $activity->program_id, $beneficiaryPools);
                continue;
            }

            $this->seedEducationParticipants($activity, (int) $activity->program_id, $beneficiaryPools);
        }
    }

    private function loadProgramStartDates(): void
    {
        $statusTypeIds = BeneficiaryStatusType::query()
            ->whereIn('status_name', ['Probation Scholar', 'Registered Player'])
            ->pluck('id', 'status_name');

        $probationTypeId = $statusTypeIds['Probation Scholar'] ?? null;
        $playerTypeId = $statusTypeIds['Registered Player'] ?? null;

        if ($probationTypeId !== null) {
            $this->probationStarts = DB::table('beneficiary_statuses')
                ->where('beneficiary_status_type_id', $probationTypeId)
                ->select('beneficiary_id', DB::raw('MIN(start_date) as start_date'))
                ->groupBy('beneficiary_id')
                ->get()
                ->keyBy('beneficiary_id')
                ->all();
        }

        if ($playerTypeId !== null) {
            $this->playerStarts = DB::table('beneficiary_statuses')
                ->where('beneficiary_status_type_id', $playerTypeId)
                ->select('beneficiary_id', DB::raw('MIN(start_date) as start_date'))
                ->groupBy('beneficiary_id')
                ->get()
                ->keyBy('beneficiary_id')
                ->all();
        }
    }

    private function loadExitDates(): void
    {
        $this->sportsExits = DB::table('beneficiary_program_memberships')
            ->join('programs', 'beneficiary_program_memberships.program_id', '=', 'programs.id')
            ->where('programs.program_name', 'Sports')
            ->whereNotNull('exited_at')
            ->pluck('exited_at', 'beneficiary_id')
            ->map(fn ($date) => $date ? Carbon::parse($date) : null)
            ->all();

        $this->educationExits = DB::table('beneficiary_program_memberships')
            ->join('programs', 'beneficiary_program_memberships.program_id', '=', 'programs.id')
            ->where('programs.program_name', 'Education')
            ->whereNotNull('exited_at')
            ->pluck('exited_at', 'beneficiary_id')
            ->map(fn ($date) => $date ? Carbon::parse($date) : null)
            ->all();
    }

    private function seedSportsParticipants(Activity $activity, int $programId, array $beneficiaryPools): void
    {
        $activityYear = SeederSupport::yearFromActivityName($activity->name) ?? now()->year;
        $ageGroup = $this->ageGroupFromActivityName($activity->name);

        $eligibleBeneficiaries = ($beneficiaryPools[$programId] ?? collect())
            ->filter(function (Beneficiary $beneficiary) use ($ageGroup, $activityYear): bool {
                $age = SeederSupport::ageAtYear($beneficiary->birth_date, $activityYear);

                return SeederSupport::matchesSportsAgeGroup($age, $ageGroup);
            })
            ->values();

        $this->attachParticipants($activity, $eligibleBeneficiaries, $this->playerStarts, $this->sportsExits, true);
    }

    private function seedEducationParticipants(Activity $activity, int $programId, array $beneficiaryPools): void
    {
        $stage = $this->educationStageFromActivityName($activity->name);
        $eligibleBeneficiaries = ($beneficiaryPools[$programId] ?? collect())
            ->filter(function (Beneficiary $beneficiary) use ($stage): bool {
                $gradeLevel = (string) ($beneficiary->intakeSheet?->grade_level ?? '');

                return match ($stage) {
                    'Elementary' => in_array($gradeLevel, ['1', '2', '3', '4', '5', '6'], true),
                    'High School' => in_array($gradeLevel, ['7', '8', '9', '10'], true),
                    'Senior High School' => in_array($gradeLevel, ['11', '12'], true),
                    default => in_array($gradeLevel, ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'], true),
                };
            })
            ->values();

        $this->attachParticipants($activity, $eligibleBeneficiaries, $this->probationStarts, $this->educationExits, false, true);
    }

    /**
     * @param  array<int, object>  $programStarts
     * @param  array<int, Carbon|null>  $exitDates
     */
    private function attachParticipants(Activity $activity, $beneficiaries, array $programStarts, array $exitDates, bool $randomizeJoinDate = false, bool $allEligible = false): void
    {
        $activityYear = SeederSupport::yearFromActivityName($activity->name) ?? now()->year;
        $activityYearStart = Carbon::create($activityYear, 1, 1);

        $eligible = $beneficiaries->filter(function (Beneficiary $beneficiary) use ($programStarts, $exitDates, $activityYear, $activityYearStart): bool {
            $startDate = $programStarts[$beneficiary->id]->start_date ?? null;

            if (SeederSupport::joinedAtForActivityYear($startDate, $activityYear) === null) {
                return false;
            }

            $exitedAt = $exitDates[$beneficiary->id] ?? null;

            if ($exitedAt !== null && $exitedAt->lt($activityYearStart)) {
                return false;
            }

            return true;
        })->values();

        if ($allEligible) {
            $chosen = $eligible;
        } else {
            $limit = min(15, max(5, (int) ceil($eligible->count() * 0.35)));
            $chosen = $eligible->shuffle()->take($limit);
        }
        $rows = [];
        $participantTimestamp = SeederSupport::activityYearTimestamp($activityYear);

        foreach ($chosen as $beneficiary) {
            $startDate = $programStarts[$beneficiary->id]->start_date ?? null;
            $joinedAt = SeederSupport::joinedAtForActivityYear($startDate, $activityYear);

            if ($joinedAt === null) {
                continue;
            }

            if ($randomizeJoinDate) {
                $joinedAt = $joinedAt->copy()->addDays(fake()->numberBetween(0, 180));
            }

            $rows[] = [
                'activity_id' => $activity->id,
                'beneficiary_id' => $beneficiary->id,
                'staff_id' => null,
                'joined_at' => $joinedAt,
                'exited_at' => null,
                'created_by' => SeederSupport::CREATOR_STAFF_ID,
                'updated_by' => SeederSupport::CREATOR_STAFF_ID,
                'created_at' => $participantTimestamp,
                'updated_at' => $participantTimestamp,
            ];
        }

        DB::table('activity_participants')->where('activity_id', $activity->id)->delete();

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('activity_participants')->insert($chunk);
        }
    }

    private function ageGroupFromActivityName(string $name): string
    {
        if (str_starts_with($name, 'U12')) {
            return 'U12';
        }

        if (str_starts_with($name, 'U14')) {
            return 'U14';
        }

        if (str_starts_with($name, 'U16')) {
            return 'U16';
        }

        return 'U18';
    }

    private function educationStageFromActivityName(string $name): string
    {
        return match (true) {
            str_starts_with($name, 'Elementary') => 'Elementary',
            str_starts_with($name, 'High School') => 'High School',
            str_starts_with($name, 'Senior High School') => 'Senior High School',
            default => 'College',
        };
    }
}
