<?php

namespace Modules\Requisition\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Requisition\Models\Requisition;
use Modules\Requisition\Models\RequisitionApprover;

class RequisitionShared
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Requisition $requisition,
        public readonly RequisitionApprover $approver,
    ) {}
}
