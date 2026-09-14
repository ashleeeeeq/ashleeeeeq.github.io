<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\SportType;
use App\Models\ActivityType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActivitiesSeeder extends Seeder
{
    public function run(): void
    {
        $startYear = 2020;
        $endYear = Carbon::now()->year;

        $programIds = Program::pluck('id', 'program_name')->all();
        $sportTypes = SportType::all();
        $activityTypeIds = ActivityType::pluck('id', 'name')->all();

        $ageGroups = ['U12', 'U14', 'U16', 'U18'];
        $educationStages = ['Elementary', 'High School', 'Senior High School', 'College'];
        $rows = [];

        for ($y = $startYear; $y <= $endYear; $y++) {
            $yearTimestamp = SeederSupport::activityYearTimestamp($y);

            foreach ($sportTypes as $sport) {
                foreach ($ageGroups as $age) {
                    $rows[] = [
                        'name' => sprintf('%s %s Training Session %d', $age, $sport->name, $y),
                        'program_id' => $programIds['Sports'] ?? null,
                        'sport_type_id' => $sport->id,
                        'activity_type_id' => $activityTypeIds['Training Session'] ?? null,
                        'description' => null,
                        'is_active' => $y < $endYear ? 0 : 1,
                        'created_by' => SeederSupport::CREATOR_STAFF_ID,
                        'updated_by' => SeederSupport::CREATOR_STAFF_ID,
                        'created_at' => $yearTimestamp,
                        'updated_at' => $yearTimestamp,
                    ];
                }
            }

            foreach ($educationStages as $stage) {
                foreach (['Tutorial Session', 'EQ Session'] as $atype) {
                    $rows[] = [
                        'name' => sprintf('%s %s %d', $stage, $atype, $y),
                        'program_id' => $programIds['Education'] ?? null,
                        'sport_type_id' => null,
                        'activity_type_id' => $activityTypeIds[$atype] ?? null,
                        'description' => null,
                        'is_active' => $y < $endYear ? 0 : 1,
                        'created_by' => SeederSupport::CREATOR_STAFF_ID,
                        'updated_by' => SeederSupport::CREATOR_STAFF_ID,
                        'created_at' => $yearTimestamp,
                        'updated_at' => $yearTimestamp,
                    ];
                }
            }
        }

        if ($rows === []) {
            return;
        }

        DB::table('activities')->whereIn('name', array_column($rows, 'name'))->delete();

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('activities')->insert($chunk);
        }
    }
}
