<?php

namespace Database\Factories;

use App\Models\EducationIntakeSheet;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducationIntakeSheetFactory extends Factory
{
    protected $model = EducationIntakeSheet::class;

    public function definition(): array
    {
        $numberOfSiblings = $this->faker->numberBetween(0, 6);

        return [
            'age' => $this->faker->numberBetween(10, 18),
            'place_of_birth' => $this->faker->city(),
            'number_of_siblings' => $numberOfSiblings,
            'older_sibling_age' => $numberOfSiblings > 0 ? $this->faker->numberBetween(19, 25) : null,
            'younger_sibling_age' => $numberOfSiblings > 0 ? $this->faker->numberBetween(1, 9) : null,
            'civil_status' => 'Single',
            'highest_education' => $this->faker->randomElement(['pre-school', 'elementary', 'high_school', 'shs', 'college']),
            'school_name' => $this->faker->company() . ' School',
            'grade_level' => (string) $this->faker->numberBetween(1, 10),
            'other_scholarship' => $this->faker->boolean(20),
            'scholarship_org_question' => 'None',
            'hardworking_question' => $this->faker->sentence(10),
            'dream_question' => $this->faker->sentence(10),
            'scholarship_question' => $this->faker->sentence(10),
        ];
    }
}
