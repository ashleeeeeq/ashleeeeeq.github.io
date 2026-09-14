<?php

namespace Database\Seeders;

use App\Models\EventType;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventsSeeder extends Seeder
{
    private const INSERT_CHUNK_SIZE = 500;

    private const EVENT_DEFINITIONS = [
        'Sports and Life Skills' => [
            ['name' => 'Life Skills Workshop %d Q1', 'month' => 2],
            ['name' => 'Sports Camp %d', 'month' => 4],
            ['name' => 'FAIRALL Sports Fest %d', 'month' => 6],
            ['name' => 'Life Skills Workshop %d Q2', 'month' => 8],
            ['name' => 'Team Building Activity %d', 'month' => 9],
            ['name' => 'Life Skills Workshop %d Q3', 'month' => 11],
        ],
        'Social' => [
            ['name' => 'Beneficiary Recognition Day %d', 'month' => 3],
            ['name' => 'Foundation Day %d', 'month' => 7],
            ['name' => 'Christmas Party %d', 'month' => 12],
        ],
        'Outreach' => [
            ['name' => 'Community Clean-Up Drive %d', 'month' => 2],
            ['name' => 'Feeding Program %d', 'month' => 6],
            ['name' => 'Back-to-School Kit Distribution %d', 'month' => 10],
        ],
    ];

    public function run(): void
    {
        $programIds = Program::pluck('id', 'program_name')->all();
        $eventTypeIds = EventType::pluck('id', 'name')->all();
        $eventTypeNames = EventType::pluck('name', 'id')->all();
        $creatorId = SeederSupport::CREATOR_STAFF_ID;

        $years = range(2020, now()->year);
        $events = [];
        $now = now();

        foreach (self::EVENT_DEFINITIONS as $eventTypeName => $definitions) {
            $eventTypeId = $eventTypeIds[$eventTypeName] ?? null;
            $programId = $eventTypeName === 'Sports and Life Skills' ? $programIds['Sports'] : null;

            foreach ($years as $year) {
                foreach ($definitions as $def) {
                    $month = $def['month'];
                    $day = random_int(5, 25);
                    $startHour = random_int(8, 10);
                    $start = Carbon::create($year, $month, $day, $startHour, 0, 0);

                    if ($start->gt(now())) {
                        continue;
                    }

                    $duration = random_int(3, 8);
                    $end = (clone $start)->addHours($duration);

                    $events[] = [
                        'name' => sprintf($def['name'], $year),
                        'program_id' => $programId,
                        'event_type_id' => $eventTypeId,
                        'description' => null,
                        'location' => $this->locationForType($eventTypeName),
                        'start' => $start->toDateTimeString(),
                        'end' => $end->toDateTimeString(),
                        'qr_token' => Str::random(40),
                        'created_by' => $creatorId,
                        'updated_by' => $creatorId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        DB::table('events')->delete();

        foreach (array_chunk($events, self::INSERT_CHUNK_SIZE) as $chunk) {
            DB::table('events')->insert($chunk);
        }

        $eventNames = array_column($events, 'name');
        $insertedEvents = DB::table('events')->whereIn('name', $eventNames)->get();

        $this->seedAttendance($insertedEvents, $programIds, $eventTypeNames, $creatorId, $now);
    }

    private function locationForType(string $eventTypeName): string
    {
        return match ($eventTypeName) {
            'Sports and Life Skills' => 'FAIRALL Sports Complex',
            'Social' => 'FAIRALL Main Campus',
            'Outreach' => random_int(0, 1) ? 'FAIRALL Main Campus' : 'FAIRALL Community Center',
            default => 'FAIRALL Main Campus',
        };
    }

    private function seedAttendance($events, array $programIds, array $eventTypeNames, int $creatorId, Carbon $now): void
    {
        if ($events->isEmpty()) {
            return;
        }

        $sportMemberships = DB::table('beneficiary_program_memberships')
            ->where('program_id', $programIds['Sports'])
            ->select(['beneficiary_id', 'enrolled_at', 'exited_at'])
            ->get()
            ->keyBy('beneficiary_id');

        $educationMemberships = DB::table('beneficiary_program_memberships')
            ->where('program_id', $programIds['Education'])
            ->select(['beneficiary_id', 'enrolled_at', 'exited_at'])
            ->get()
            ->keyBy('beneficiary_id');

        $methods = ['QR', 'Staff'];
        $allRows = [];

        foreach ($events as $event) {
            $eventStart = Carbon::parse($event->start);
            $eventProgramId = $event->program_id;
            $typeName = $eventTypeNames[$event->event_type_id] ?? '';

            $eligible = [];

            if ($eventProgramId === null) {
                foreach ($sportMemberships as $bid => $m) {
                    if ($this->isEligible($m, $eventStart)) {
                        $eligible[] = (int) $bid;
                    }
                }
                foreach ($educationMemberships as $bid => $m) {
                    if ($this->isEligible($m, $eventStart)) {
                        $eligible[] = (int) $bid;
                    }
                }
            } else {
                $memberships = $eventProgramId === (int) $programIds['Sports']
                    ? $sportMemberships
                    : $educationMemberships;

                foreach ($memberships as $bid => $m) {
                    if ($this->isEligible($m, $eventStart)) {
                        $eligible[] = (int) $bid;
                    }
                }
            }

            $eligible = array_unique($eligible);

            if ($eligible === []) {
                continue;
            }

            $attendancePct = match ($typeName) {
                'Sports and Life Skills' => random_int(70, 85),
                'Social' => random_int(50, 75),
                'Outreach' => random_int(40, 60),
                default => random_int(50, 75),
            };

            $attending = collect($eligible)
                ->shuffle()
                ->take((int) ceil(count($eligible) * $attendancePct / 100))
                ->all();

            foreach ($attending as $beneficiaryId) {
                $allRows[] = [
                    'beneficiary_id' => $beneficiaryId,
                    'staff_id' => null,
                    'activity_session_id' => null,
                    'event_id' => $event->id,
                    'attendance_status' => self::randomAttendanceStatus(),
                    'remarks' => null,
                    'attendance_method' => $methods[random_int(0, 1)],
                    'created_by' => $creatorId,
                    'updated_by' => $creatorId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if ($allRows === []) {
            return;
        }

        foreach (array_chunk($allRows, self::INSERT_CHUNK_SIZE) as $chunk) {
            DB::table('attendances')->insert($chunk);
        }
    }

    private function isEligible(object $membership, Carbon $eventStart): bool
    {
        $enrolledAt = $membership->enrolled_at !== null
            ? Carbon::parse($membership->enrolled_at)
            : null;

        if ($enrolledAt === null || $enrolledAt->gt($eventStart)) {
            return false;
        }

        $exitedAt = $membership->exited_at !== null
            ? Carbon::parse($membership->exited_at)
            : null;

        if ($exitedAt !== null && $exitedAt->lt($eventStart)) {
            return false;
        }

        return true;
    }

    private static function randomAttendanceStatus(): string
    {
        $roll = random_int(1, 100);

        if ($roll <= 87) {
            return 'present';
        }

        if ($roll <= 92) {
            return 'late';
        }

        if ($roll <= 97) {
            return 'absent';
        }

        return 'excused';
    }
}
