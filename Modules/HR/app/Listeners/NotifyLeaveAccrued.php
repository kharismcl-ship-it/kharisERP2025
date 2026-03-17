<?php

namespace Modules\HR\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\HR\Events\LeaveAccrued;

class NotifyLeaveAccrued
{
    public function __construct(
        protected CommunicationService $communicationService
    ) {}

    public function handle(LeaveAccrued $event): void
    {
        $leaveBalance = $event->leaveBalance;
        $employee     = $leaveBalance->employee;

        if (! $employee || ! $employee->email) {
            return;
        }

        try {
            $leaveType = $leaveBalance->leaveType?->name ?? 'Leave';
            $body = "Dear {$employee->full_name},\n\n"
                . "{$event->accruedDays} day(s) of {$leaveType} have been accrued to your leave balance.\n"
                . "Your new balance is {$leaveBalance->current_balance} days.\n\n"
                . "Please contact HR if you have any questions.";

            $this->communicationService->sendRawEmail(
                $employee->email,
                $employee->full_name,
                "Leave Accrual: {$event->accruedDays} day(s) of {$leaveType} added",
                $body,
            );
        } catch (\Throwable $e) {
            Log::warning('NotifyLeaveAccrued: Failed to send accrual notification.', [
                'leave_balance_id' => $leaveBalance->id,
                'error'            => $e->getMessage(),
            ]);
        }
    }
}
