<?php

namespace Database\Factories;

use App\Models\BeneficiaryGuardian;
use Illuminate\Database\Eloquent\Factories\Factory;

class BeneficiaryGuardianFactory extends Factory
{
    protected $model = BeneficiaryGuardian::class;

    public function definition(): array
    {
        $sex = $this->faker->randomElement(['male', 'female']);
        $type = $this->faker->randomElement(['mother', 'father', 'guardian']);
        $jobs = [
            'Vendor',
            'Laundry Helper',
            'Construction Worker',
            'Driver',
            'Security Guard',
            'Teacher',
            'Factory Worker',
            'Store Clerk',
            'Street Vendor',
            'House Helper',
        ];

        return [
            'guardian_type' => $type,
            'first_name' => $this->faker->firstName($sex),
            'middle_name' => $this->faker->optional()->firstName(),
            'last_name' => $this->faker->lastName(),
            'civil_status' => $this->faker->randomElement(['Single', 'Married', 'Widowed', 'Separated']),
            'birth_date' => $this->faker->dateTimeBetween('-60 years', '-20 years')->format('Y-m-d'),
            'place_of_birth' => 'Payatas, Quezon City, Metro Manila',
            'sex' => $sex,
            'contact_number' => '9' . $this->faker->numerify('#########'),
            'dial_code' => '+63',
            'highest_education' => $this->faker->randomElement(['pre-school', 'elementary', 'high_school', 'shs', 'college', 'post-grad']),
            'job' => $this->faker->randomElement($jobs),
            'estimated_salary' => (string) $this->faker->numberBetween(5000, 50000),
            'deceased' => $this->faker->boolean(2),
        ];
    }
}
