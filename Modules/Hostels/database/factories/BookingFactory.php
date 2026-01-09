<?php

namespace Modules\Hostels\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hostels\Models\Bed;
use Modules\Hostels\Models\Booking;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Models\Room;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $checkIn = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $checkOut = $this->faker->dateTimeBetween($checkIn, '+3 months');

        return [
            'hostel_id' => Hostel::factory(),
            'room_id' => Room::factory(),
            'bed_id' => Bed::factory(),
            'hostel_occupant_id' => HostelOccupant::factory(),
            'booking_reference' => $this->faker->unique()->regexify('[A-Z]{3}-[0-9]{6}'),
            'booking_type' => $this->faker->randomElement(['academic', 'short_stay', 'semester']),
            'academic_year' => $this->faker->optional()->year,
            'semester' => $this->faker->optional()->randomElement(['1', '2']),
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'status' => $this->faker->randomElement(['pending', 'awaiting_payment', 'confirmed', 'checked_in', 'checked_out', 'no_show', 'cancelled']),
            'total_amount' => $this->faker->randomFloat(2, 100, 5000),
            'amount_paid' => $this->faker->randomFloat(2, 0, 5000),
            'balance_amount' => function (array $attributes) {
                return $attributes['total_amount'] - $attributes['amount_paid'];
            },
            'payment_status' => function (array $attributes) {
                if ($attributes['amount_paid'] == 0) {
                    return 'unpaid';
                }
                if ($attributes['amount_paid'] < $attributes['total_amount']) {
                    return 'partially_paid';
                }
                if ($attributes['amount_paid'] == $attributes['total_amount']) {
                    return 'paid';
                }

                return 'overpaid';
            },
            'channel' => $this->faker->randomElement(['walk_in', 'online', 'agent']),
            'notes' => $this->faker->optional()->sentence,
        ];
    }
}
