<?php

namespace Modules\HR\Database\factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Models\Employee;
use Modules\HR\Models\PerformanceCycle;
use Modules\HR\Models\PerformanceReview;

class PerformanceReviewFactory extends Factory
{
    protected $model = PerformanceReview::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'performance_cycle_id' => PerformanceCycle::factory(),
            'employee_id' => Employee::factory(),
            'reviewer_employee_id' => Employee::factory(),
            'rating' => $this->faker->randomFloat(2, 1, 5),
            'comments' => $this->faker->optional()->paragraph(),
        ];
    }
}
