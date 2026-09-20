<?php

namespace Database\Factories;

use App\Models\SubjectGrade;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectGradeFactory extends Factory
{
    protected $model = SubjectGrade::class;

    public function definition(): array
    {
        return [
            'subject_name' => $this->faker->word(),
            'grade' => $this->faker->randomFloat(2, 75, 98),
        ];
    }
}
