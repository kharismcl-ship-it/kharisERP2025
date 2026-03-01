<?php

namespace Modules\Fleet\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Fleet\Models\MaintenanceRecord;

class MaintenanceCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly MaintenanceRecord $maintenanceRecord,
    ) {}
}
