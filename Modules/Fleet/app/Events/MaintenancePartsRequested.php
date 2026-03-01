<?php

namespace Modules\Fleet\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Fleet\Models\MaintenanceRecord;

class MaintenancePartsRequested
{
    use SerializesModels;

    /**
     * @param  array<array{description: string, quantity: float, unit_price: float}>  $parts
     */
    public function __construct(
        public MaintenanceRecord $maintenanceRecord,
        public array $parts = []
    ) {}
}
