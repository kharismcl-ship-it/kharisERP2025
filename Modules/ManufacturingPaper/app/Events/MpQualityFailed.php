<?php

namespace Modules\ManufacturingPaper\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\ManufacturingPaper\Models\MpQualityRecord;

class MpQualityFailed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly MpQualityRecord $qualityRecord,
    ) {}
}