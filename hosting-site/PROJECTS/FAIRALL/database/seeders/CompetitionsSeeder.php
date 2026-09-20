<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Competition;
use App\Models\Program;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CompetitionsSeeder extends Seeder
{
    public function run(): void
    {
        $startYear = 2020;
        $endYear = Carbon::now()->year;

        $programs = Program::all();

        $types = ['sports & athletic', 'academic', 'technical & professional', 'creative & leisure'];
        $scales = ['local', 'regional', 'national', 'international'];

        $created = [];

        foreach ($programs as $program) {
            for ($y = $startYear; $y <= $endYear; $y++) {
                for ($n = 1; $n <= 2; $n++) {
                    $name = sprintf('%s %s Competition %d #%d', $program->program_name, Str::title($types[array_rand($types)]), $y, $n);

                    $maxMonth = $y < now()->year ? 12 : now()->month;
                    $start = Carbon::create($y, rand(1, $maxMonth), rand(1, 26), rand(8, 11), 0, 0);
                    $end = (clone $start)->addHours(rand(4, 10));

                    $competition = Competition::updateOrCreate(
                        [
                            'name' => $name,
                            'program_id' => $program->id,
                        ],
                        [
                            'type' => $types[array_rand($types)],
                            'scale' => $scales[array_rand($scales)],
                            'organizer' => $program->program_name . ' Program',
                            'description' => null,
                            'venue' => $program->program_name . ' Venue',
                            'start' => $start->toDateTimeString(),
                            'end' => $end->toDateTimeString(),
                        ]
                    );

                    $created[] = $competition->id;
                }
            }
        }

        // optionally remove competitions not in created list - skipped
    }
}
