<?php

namespace Modules\Hostels\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\Room;

class BedFactory extends Factory
{
    protected $model = Bed::class;

    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'bed_number' => $this->faker->unique()->randomNumber(),
            'status' => 'available',
        ];
    }
}
