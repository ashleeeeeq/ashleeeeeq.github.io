<?php

namespace Database\Factories;

use App\Models\Beneficiary;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BeneficiaryFactory extends Factory
{
    protected $model = Beneficiary::class;

    public function definition(): array
    {
        $sex = $this->faker->randomElement(['male', 'female']);
        $birthDate = $this->faker->dateTimeBetween('-18 years', '-8 years')->format('Y-m-d');

        return [
            'user_id' => User::factory()->state(['password' => 'Password123!']),
            'first_name' => $this->faker->firstName($sex),
            'middle_name' => $this->faker->optional()->firstName(),
            'last_name' => $this->faker->lastName(),
            'birth_date' => $birthDate,
            'sex' => $sex,
            'contact_number' => '9' . $this->faker->numerify('#########'),
            'dial_code' => '+63',
            'form_given' => $this->faker->boolean(90),
            'with_disability' => $this->faker->boolean(5),
        ];
    }

    public function sports(): static
    {
        return $this->state(fn() => []);
    }

    public function education(): static
    {
        return $this->state(fn() => []);
    }

    public function mixed(): static
    {
        return $this->state(fn() => []);
    }
}
