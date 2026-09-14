<?php

namespace Database\Seeders;

use App\Models\Competition;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CompetitionResultsSeeder extends Seeder
{
    public function run(): void
    {
        $adminStaffId = $this->adminStaffId();
        $placements = ['1st', '2nd', '3rd', 'Participant'];

        Competition::query()
            ->with('program')
            ->orderBy('id')
            ->chunkById(25, function ($competitions) use ($adminStaffId, $placements): void {
                $rows = [];
                $competitionIds = [];

                foreach ($competitions as $competition) {
                    $competitionIds[] = $competition->id;
                    $eligibleBeneficiaryIds = DB::table('beneficiary_program_memberships')
                        ->where('program_id', $competition->program_id)
                        ->where('enrolled_at', '<=', $competition->start)
                        ->pluck('beneficiary_id')
                        ->unique()
                        ->shuffle()
                        ->take(4)
                        ->all();

                    if ($eligibleBeneficiaryIds === []) {
                        continue;
                    }

                    $windowStart = Carbon::parse($competition->start);
                    $windowEnd = Carbon::parse($competition->end ?? $competition->start);
                    $windowMinutes = max(1, $windowStart->diffInMinutes($windowEnd));

                    foreach ($eligibleBeneficiaryIds as $index => $beneficiaryId) {
                        $dateGiven = $windowStart->copy()->addMinutes(random_int(0, $windowMinutes));
                        $rows[] = [
                            'competition_id' => $competition->id,
                            'beneficiary_id' => $beneficiaryId,
                            'placement' => $placements[$index] ?? 'Participant',
                            'date_given' => $dateGiven->toDateString(),
                            'created_by' => $adminStaffId,
                            'updated_by' => $adminStaffId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if ($competitionIds !== []) {
                    DB::table('competition_results')->whereIn('competition_id', $competitionIds)->delete();
                }

                foreach (array_chunk($rows, 500) as $chunk) {
                    DB::table('competition_results')->insert($chunk);
                }
            });
    }

    private function adminStaffId(): int
    {
        return SeederSupport::CREATOR_STAFF_ID;
    }
}
