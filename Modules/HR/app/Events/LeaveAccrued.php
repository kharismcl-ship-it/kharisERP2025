<?php

namespace Modules\HR\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\HR\Models\LeaveBalance;

class LeaveAccrued
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public LeaveBalance $leaveBalance;

    public float $accruedDays;

    public ?string $effectiveDate;

    public function __construct(LeaveBalance $leaveBalance, float $accruedDays, ?string $effectiveDate = null)
    {
        $this->leaveBalance = $leaveBalance;
        $this->accruedDays = $accruedDays;
        $this->effectiveDate = $effectiveDate;
    }
}
