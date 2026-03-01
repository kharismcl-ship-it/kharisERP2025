<?php

namespace Modules\HR\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\HR\Models\LeaveBalance;

class LeaveBalanceUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public LeaveBalance $leaveBalance;

    public array $changedFields;

    public function __construct(LeaveBalance $leaveBalance)
    {
        $this->leaveBalance = $leaveBalance;
        $this->changedFields = $leaveBalance->getDirty();
    }
}
