<?php

namespace Modules\ManufacturingWater\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\ManufacturingWater\Models\MwDistributionRecord;

class MwDistributionCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly MwDistributionRecord $distributionRecord,
    ) {}
}