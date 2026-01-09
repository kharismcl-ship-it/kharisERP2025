<?php

namespace Modules\Hostels\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\Tenant;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'bed_id' => Bed::factory(),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'status' => 'active',
        ];
    }
}
