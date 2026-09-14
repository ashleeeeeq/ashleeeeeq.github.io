<?php

namespace Database\Factories;

use App\Models\FfaAssessmentRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class FfaAssessmentRecordFactory extends Factory
{
    protected $model = FfaAssessmentRecord::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'score' => $this->faker->randomFloat(2, 70, 98),
            'max_score' => 100,
            'remarks' => $this->faker->sentence(),
            'date' => $this->faker->date(),
        ];
    }
}
