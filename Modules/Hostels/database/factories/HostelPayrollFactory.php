<?php

namespace Modules\Hostels\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hostels\Models\HostelPayroll;

class HostelPayrollFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HostelPayroll::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hostel_id' => null, // Will be set in seeder
            'period_start_date' => $this->faker->dateTimeBetween('-1 year', '-1 month'),
            'period_end_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'total_staff_count' => $this->faker->numberBetween(5, 50),
            'total_hours_worked' => $this->faker->numberBetween(100, 2000),
            'total_payroll_amount' => $this->faker->randomFloat(2, 1000, 50000),
            'status' => $this->faker->randomElement(['pending', 'processed', 'exported', 'completed']),
            'synced_at' => $this->faker->optional(0.7)->dateTimeBetween('-1 month', 'now'),
            'exported_at' => $this->faker->optional(0.5)->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
