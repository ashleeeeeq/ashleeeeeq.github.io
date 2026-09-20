<?php

namespace Database\Factories;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        return [
            'staff_id' => null,
            'beneficiary_id' => null,
            'activity_session_id' => null,
            'event_id' => null,
            'attendance_status' => $this->faker->randomElement(['present', 'absent', 'late', 'excused']),
            'remarks' => $this->faker->optional()->sentence(),
            'attendance_method' => $this->faker->randomElement(['QR', 'Staff']),
        ];
    }
}
