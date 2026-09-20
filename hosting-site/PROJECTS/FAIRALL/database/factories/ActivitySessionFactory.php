<?php

namespace Database\Factories;

use App\Models\ActivitySession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ActivitySessionFactory extends Factory
{
    protected $model = ActivitySession::class;

    public function definition(): array
    {
        return [
            'activity_id' => null,
            'schedule' => $this->faker->dateTimeBetween('-2 years', '+1 year'),
            'qr_token' => Str::random(32),
        ];
    }
}
