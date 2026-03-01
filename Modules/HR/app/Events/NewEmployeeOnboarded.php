<?php

namespace Modules\HR\Events;

use Illuminate\Queue\SerializesModels;
use Modules\HR\Models\Employee;

class NewEmployeeOnboarded
{
    use SerializesModels;

    /**
     * @param  array<array{description: string, quantity: float, unit_price: float}>  $onboardingItems
     */
    public function __construct(
        public Employee $employee,
        public array $onboardingItems = []
    ) {}
}
