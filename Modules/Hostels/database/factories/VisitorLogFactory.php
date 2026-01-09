<?php

namespace Modules\Hostels\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Models\VisitorLog;

class VisitorLogFactory extends Factory
{
    protected $model = VisitorLog::class;

    public function definition(): array
    {
        return [
            'hostel_id' => Hostel::factory(),
            'hostel_occupant_id' => HostelOccupant::factory(),
            'visitor_name' => $this->faker->name,
            'visitor_phone' => $this->faker->phoneNumber,
            'purpose' => $this->faker->sentence(),
            'check_in_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'check_out_at' => $this->faker->optional()->dateTimeBetween('-30 days', 'now'),
            'recorded_by_user_id' => User::factory(),
        ];
    }
}
