<?php

namespace Database\Factories;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'program_id' => null,
            'sport_type_id' => null,
            'activity_type_id' => null,
            'description' => $this->faker->optional()->sentence(),
            'is_active' => $this->faker->boolean(95),
        ];
    }
}
