<?php

namespace Modules\Requisition\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Requisition\Models\Requisition;

class RequisitionStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Requisition $requisition,
        public readonly string $oldStatus,
    ) {}
}
