<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttendanceSeeder extends Seeder
{
    private const INSERT_CHUNK_SIZE = 3000;

    public function run(): void
    {
        if (! Schema::hasTable('attendances') || ! Schema::hasTable('activity_participants')) {
            return;
        }

        $creatorId = SeederSupport::CREATOR_STAFF_ID;
        $timestamp = now()->toDateTimeString();

        Activity::query()
            ->orderBy('id')
            ->select('id')
            ->chunkById(20, function ($activities) use ($creatorId, $timestamp): void {
                foreach ($activities as $activity) {
                    $this->seedAttendanceForActivity((int) $activity->id, $creatorId, $timestamp);
                }
            });
    }

    private function seedAttendanceForActivity(int $activityId, int $creatorId, string $timestamp): void
    {
        $participants = DB::table('activity_participants')
            ->where('activity_id', $activityId)
            ->whereNotNull('beneficiary_id')
            ->select(['beneficiary_id', 'joined_at', 'exited_at'])
            ->get();

        if ($participants->isEmpty()) {
            return;
        }

        $sessions = DB::table('activity_sessions')
            ->where('activity_id', $activityId)
            ->orderBy('schedule')
            ->select(['id', 'schedule'])
            ->get();

        if ($sessions->isEmpty()) {
            return;
        }

        /** @var list<array{id: int, ts: int}> $sessionIndex */
        $sessionIndex = [];
        foreach ($sessions as $session) {
            $sessionIndex[] = [
                'id' => (int) $session->id,
                'ts' => strtotime((string) $session->schedule),
            ];
        }

        $sessionCount = count($sessionIndex);
        $rows = [];
        $methods = ['QR', 'Staff'];

        foreach ($participants as $participant) {
            $joinedTs = strtotime((string) $participant->joined_at);
            $exitedTs = $participant->exited_at !== null
                ? strtotime((string) $participant->exited_at)
                : null;

            $start = $this->firstSessionIndexAtOrAfter($sessionIndex, $joinedTs, $sessionCount);
            if ($start >= $sessionCount) {
                continue;
            }

            $end = $exitedTs !== null
                ? $this->lastSessionIndexAtOrBefore($sessionIndex, $exitedTs, $sessionCount)
                : $sessionCount - 1;

            if ($start > $end) {
                continue;
            }

            for ($i = $start; $i <= $end; $i++) {
                $rows[] = [
                    'beneficiary_id' => (int) $participant->beneficiary_id,
                    'activity_session_id' => $sessionIndex[$i]['id'],
                    'attendance_status' => $this->randomAttendanceStatus(),
                    'attendance_method' => $methods[random_int(0, 1)],
                    'created_by' => $creatorId,
                    'updated_by' => $creatorId,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];

                if (count($rows) >= self::INSERT_CHUNK_SIZE) {
                    DB::table('attendances')->insert($rows);
                    $rows = [];
                }
            }
        }

        if ($rows !== []) {
            DB::table('attendances')->insert($rows);
        }
    }

    /**
     * @param  list<array{id: int, ts: int}>  $sessions
     */
    private function firstSessionIndexAtOrAfter(array $sessions, int $timestamp, int $count): int
    {
        $lo = 0;
        $hi = $count - 1;
        $result = $count;

        while ($lo <= $hi) {
            $mid = intdiv($lo + $hi, 2);

            if ($sessions[$mid]['ts'] >= $timestamp) {
                $result = $mid;
                $hi = $mid - 1;
            } else {
                $lo = $mid + 1;
            }
        }

        return $result;
    }

    /**
     * @param  list<array{id: int, ts: int}>  $sessions
     */
    private function lastSessionIndexAtOrBefore(array $sessions, int $timestamp, int $count): int
    {
        $lo = 0;
        $hi = $count - 1;
        $result = -1;

        while ($lo <= $hi) {
            $mid = intdiv($lo + $hi, 2);

            if ($sessions[$mid]['ts'] <= $timestamp) {
                $result = $mid;
                $lo = $mid + 1;
            } else {
                $hi = $mid - 1;
            }
        }

        return $result;
    }

    private function randomAttendanceStatus(): string
    {
        $roll = random_int(1, 100);

        if ($roll <= 90) {
            return 'present';
        }

        if ($roll <= 95) {
            return 'late';
        }

        if ($roll <= 99) {
            return 'absent';
        }

        return 'excused';
    }
}
