<?php

namespace Database\Factories;

use App\Models\ActivityParticipant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityParticipantFactory extends Factory
{
    protected $model = ActivityParticipant::class;

    public function definition(): array
    {
        return [
            'activity_id' => null,
            'beneficiary_id' => null,
            'staff_id' => null,
            'joined_at' => $this->faker->dateTimeBetween('-3 years', 'now'),
            'exited_at' => null,
        ];
    }
}
