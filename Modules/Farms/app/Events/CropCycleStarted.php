<?php

namespace Modules\Farms\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Farms\Models\CropCycle;

class CropCycleStarted
{
    use SerializesModels;

    /**
     * @param  array<array{description: string, quantity: float, unit_price: float}>  $requiredInputs
     */
    public function __construct(
        public CropCycle $cropCycle,
        public array $requiredInputs = []
    ) {}
}
