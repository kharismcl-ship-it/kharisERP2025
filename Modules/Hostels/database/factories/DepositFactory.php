<?php

namespace Modules\Hostels\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hostels\Models\Deposit;

class DepositFactory extends Factory
{
    protected $model = Deposit::class;

    public function definition(): array
    {
        return [
            'hostel_occupant_id' => null, // Will be set in tests
            'booking_id' => null, // Will be set in tests
            'hostel_id' => null, // Will be set in tests
            'invoice_id' => null,
            'journal_entry_id' => null,
            'deposit_type' => $this->faker->randomElement(['security', 'damage', 'cleaning', 'utility']),
            'amount' => $this->faker->randomFloat(2, 100, 2000),
            'status' => Deposit::STATUS_PENDING,
            'collected_date' => null,
            'refunded_date' => null,
            'refund_amount' => 0,
            'deductions' => 0,
            'deduction_reason' => null,
            'notes' => $this->faker->sentence(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Deposit::STATUS_PENDING,
            'collected_at' => null,
            'refunded_at' => null,
            'forfeited_at' => null,
        ]);
    }

    public function collected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Deposit::STATUS_COLLECTED,
            'collected_date' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
            'refunded_date' => null,
        ]);
    }

    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Deposit::STATUS_REFUNDED,
            'collected_date' => $this->faker->dateTimeBetween('-2 months', '-1 month'),
            'refunded_date' => $this->faker->dateTimeBetween('-1 week', '-1 day'),
            'refund_amount' => $attributes['amount'],
            'deduction_reason' => ['reason' => 'End of occupancy'],
        ]);
    }

    public function partialRefund(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Deposit::STATUS_PARTIAL_REFUND,
            'collected_date' => $this->faker->dateTimeBetween('-2 months', '-1 month'),
            'refunded_date' => $this->faker->dateTimeBetween('-1 week', '-1 day'),
            'refund_amount' => $attributes['amount'] * 0.8,
            'deductions' => $attributes['amount'] * 0.2,
            'deduction_reason' => ['reason' => 'Deduction for damages', 'amount' => $attributes['amount'] * 0.2],
        ]);
    }

    public function forfeited(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Deposit::STATUS_FORFEITED,
            'collected_date' => $this->faker->dateTimeBetween('-2 months', '-1 month'),
            'refunded_date' => null,
            'refund_amount' => 0,
            'deductions' => $attributes['amount'],
            'deduction_reason' => ['reason' => 'Abandoned property'],
        ]);
    }
}
