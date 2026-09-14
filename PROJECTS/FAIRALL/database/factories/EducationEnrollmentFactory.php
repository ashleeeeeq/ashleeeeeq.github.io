<?php

namespace Database\Factories;

use App\Models\EducationEnrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducationEnrollmentFactory extends Factory
{
    protected $model = EducationEnrollment::class;

    public function definition(): array
    {
        $startYear = $this->faker->numberBetween(2020, (int) now()->year);

        return [
            'school_name' => $this->faker->company() . ' School',
            'academic_year_start_date' => $startYear . '-06-01',
            'academic_year_end_date' => ($startYear + 1) . '-03-31',
            'education_level' => $this->faker->randomElement(['elementary', 'high_school', 'shs', 'college']),
            'grade_level' => (string) $this->faker->numberBetween(1, 10),
            'enrollment_status' => 'completed',
        ];
    }
}
