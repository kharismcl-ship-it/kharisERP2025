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
            'description' => $this->faker->sentence,
        ];
    }
}
