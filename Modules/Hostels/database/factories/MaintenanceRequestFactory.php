<?php

namespace Modules\Hostels\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Models\MaintenanceRequest;
use Modules\Hostels\Models\Room;

class MaintenanceRequestFactory extends Factory
{
    protected $model = MaintenanceRequest::class;

    public function definition(): array
    {
        return [
            'hostel_id' => Hostel::factory(),
            'room_id' => Room::factory(),
            'bed_id' => Bed::factory(),
            'reported_by_hostel_occupant_id' => HostelOccupant::factory(),
            'reported_by_user_id' => User::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'cancelled']),
            'assigned_to_user_id' => User::factory(),
            'reported_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'completed_at' => $this->faker->optional()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
