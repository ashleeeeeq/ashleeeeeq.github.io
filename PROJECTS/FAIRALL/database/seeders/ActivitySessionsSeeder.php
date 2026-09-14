<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\ActivitySession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ActivitySessionsSeeder extends Seeder
{
    public function run(): void
    {
        $startYear = 2020;
        $endYear = Carbon::now()->year;

        Activity::query()
            ->with('activityType')
            ->orderBy('id')
            ->chunkById(25, function ($activities) use ($startYear, $endYear): void {
                foreach ($activities as $activity) {
                    // try to determine year from name, fallback to range
                    if (preg_match('/(\d{4})$/', $activity->name, $m)) {
                        $years = [(int) $m[1]];
                    } else {
                        $years = range($startYear, $endYear);
                    }

                    foreach ($years as $year) {
                        $from = Carbon::create($year, 1, 1, 8, 0, 0);
                        $to = $year < now()->year
                            ? Carbon::create($year, 12, 31, 23, 59, 59)
                            : Carbon::now();

                        $cadence = strtolower($activity->activityType->name ?? 'training');

                        if (str_contains($cadence, 'training')) {
                            // daily with small random gaps
                            $rows = [];

                            for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
                                if (rand(1, 100) <= 15) { // 15% chance skip (holiday/rainy)
                                    continue;
                                }

                                $rows[] = [
                                    'activity_id' => $activity->id,
                                    'schedule' => $date->toDateTimeString(),
                                    'qr_token' => Str::random(40),
                                    'created_by' => SeederSupport::CREATOR_STAFF_ID,
                                    'updated_by' => SeederSupport::CREATOR_STAFF_ID,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];

                                if (count($rows) >= 500) {
                                    $this->insertMissingSessions($rows);
                                    $rows = [];
                                }
                            }

                            $this->insertMissingSessions($rows);
                        } elseif (str_contains($cadence, 'tutorial')) {
                            // twice a week: choose two weekdays (Tue ISO=2, Thu ISO=4)
                            $dates = $this->generateWeeklyDates($from, $to, [2, 4]);
                            $rows = [];

                            foreach ($dates as $d) {
                                $rows[] = [
                                    'activity_id' => $activity->id,
                                    'schedule' => $d->toDateTimeString(),
                                    'qr_token' => Str::random(40),
                                    'created_by' => SeederSupport::CREATOR_STAFF_ID,
                                    'updated_by' => SeederSupport::CREATOR_STAFF_ID,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];

                                if (count($rows) >= 500) {
                                    $this->insertMissingSessions($rows);
                                    $rows = [];
                                }
                            }

                            $this->insertMissingSessions($rows);
                        } else {
                            // EQ Session -> 2x week (Wed=3, Fri=5)
                            $dates = $this->generateWeeklyDates($from, $to, [3, 5]);
                            $rows = [];

                            foreach ($dates as $d) {
                                $rows[] = [
                                    'activity_id' => $activity->id,
                                    'schedule' => $d->toDateTimeString(),
                                    'qr_token' => Str::random(40),
                                    'created_by' => SeederSupport::CREATOR_STAFF_ID,
                                    'updated_by' => SeederSupport::CREATOR_STAFF_ID,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];

                                if (count($rows) >= 500) {
                                    $this->insertMissingSessions($rows);
                                    $rows = [];
                                }
                            }

                            $this->insertMissingSessions($rows);
                        }
                    }
                }
            });
    }

    private function generateWeeklyDates(Carbon $from, Carbon $to, array $weekdays): array
    {
        $dates = [];
        $cursor = $from->copy()->startOfDay();

        while ($cursor->lte($to)) {
            if (in_array($cursor->isoWeekday(), $weekdays, true)) {
                $dates[] = $cursor->copy()->setTime(9, 0);
            }

            $cursor->addDay();
        }

        return $dates;
    }

    /**
     * Insert only sessions that do not already exist for the same activity and schedule.
     */
    private function insertMissingSessions(array $rows): void
    {
        if ($rows === []) {
            return;
        }

        $activityId = $rows[0]['activity_id'];
        $schedules = collect($rows)->pluck('schedule')->all();

        $existingSchedules = DB::table('activity_sessions')
            ->where('activity_id', $activityId)
            ->whereIn('schedule', $schedules)
            ->pluck('schedule')
            ->all();

        $existingLookup = array_fill_keys($existingSchedules, true);

        $filtered = array_values(array_filter(
            $rows,
            static fn (array $row): bool => ! isset($existingLookup[$row['schedule']])
        ));

        if ($filtered !== []) {
            DB::table('activity_sessions')->insert($filtered);
        }
    }
}
