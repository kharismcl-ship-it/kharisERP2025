<?php

namespace Modules\Hostels\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\Room;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'hostel_id' => Hostel::factory(),
            'room_number' => $this->faker->unique()->randomNumber(),
            'type' => $this->faker->randomElement(['single', 'double', 'triple', 'quad']),
            'base_rate' => $this->faker->randomFloat(2, 100, 500),
            'per_night_rate' => $this->faker->randomFloat(2, 50, 200),
            'per_semester_rate' => $this->faker->randomFloat(2, 1000, 3000),
            'per_year_rate' => $this->faker->randomFloat(2, 2000, 6000),
            'status' => $this->faker->randomElement(['available', 'occupied', 'maintenance', 'closed']),
            'gender_policy' => $this->faker->randomElement(['male', 'female', 'mixed', 'inherit_hostel']),
            'billing_cycle' => $this->faker->randomElement(['per_night', 'per_semester', 'per_year']),
            'max_occupancy' => $this->faker->numberBetween(1, 4),
            'current_occupancy' => 0,
            'notes' => $this->faker->optional()->sentence,
        ];
    }
}
