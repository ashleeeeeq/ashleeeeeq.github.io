<?php

namespace Database\Factories;

use App\Models\AcademicRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcademicRecordFactory extends Factory
{
    protected $model = AcademicRecord::class;

    public function definition(): array
    {
        return [
            'term' => '4th',
            'gwa' => $this->faker->randomFloat(2, 80, 100),
            'school_attendance' => $this->faker->randomFloat(2, 80, 100),
        ];
    }
}
