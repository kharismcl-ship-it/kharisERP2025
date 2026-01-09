<?php

namespace Modules\Hostels\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Models\Incident;
use Modules\Hostels\Models\Room;

class IncidentFactory extends Factory
{
    protected $model = Incident::class;

    public function definition(): array
    {
        return [
            'hostel_id' => Hostel::factory(),
            'hostel_occupant_id' => HostelOccupant::factory(),
            'room_id' => Room::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'severity' => $this->faker->randomElement(['low', 'medium', 'high', 'critical']),
            'reported_by_user_id' => User::factory(),
            'action_taken' => $this->faker->optional()->paragraph(),
            'status' => $this->faker->randomElement(['reported', 'investigating', 'resolved', 'closed']),
            'reported_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'resolved_at' => $this->faker->optional()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
