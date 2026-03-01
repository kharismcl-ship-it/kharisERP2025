<?php

namespace Modules\HR\Services;

use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveRequest;

class LeaveNotificationService
{
    protected CommunicationService $communicationService;

    public function __construct(CommunicationService $communicationService)
    {
        $this->communicationService = $communicationService;
    }

    /**
     * Send notification for leave request submission
     */
    public function notifyLeaveRequestSubmitted(LeaveRequest $leaveRequest): void
    {
        $employee = $leaveRequest->employee;
        $manager = $employee->manager ?? $employee->department?->manager;
        $leaveType = $leaveRequest->leaveType;

        if ($manager) {
            $data = [
                'employee_name' => $employee->full_name,
                'manager_name' => $manager->full_name,
                'leave_type' => $leaveType->name,
                'start_date' => $leaveRequest->start_date->format('M j, Y'),
                'end_date' => $leaveRequest->end_date->format('M j, Y'),
                'duration' => $leaveRequest->duration_in_days,
                'reason' => $leaveRequest->reason,
            ];

            // Send notification to manager through all enabled channels
            $this->communicationService->sendToModelThroughEnabledChannels(
                $manager,
                'leave_request_submitted',
                $data
            );
        }
    }

    /**
     * Send notification for leave request approval
     */
    public function notifyLeaveRequestApproved(LeaveRequest $leaveRequest): void
    {
        $employee = $leaveRequest->employee;
        $manager = $employee->manager ?? $employee->department?->manager;
        $leaveType = $leaveRequest->leaveType;

        // Get remaining balance
        $remainingBalance = $employee->leaveBalances()
            ->where('leave_type_id', $leaveType->id)
            ->where('year', now()->year)
            ->value('current_balance') ?? 0;

        $data = [
            'employee_name' => $employee->full_name,
            'manager_name' => $manager?->full_name ?? 'Manager',
            'leave_type' => $leaveType->name,
            'start_date' => $leaveRequest->start_date->format('M j, Y'),
            'end_date' => $leaveRequest->end_date->format('M j, Y'),
            'duration' => $leaveRequest->duration_in_days,
            'remaining_balance' => $remainingBalance,
        ];

        // Send notification to employee through all enabled channels
        // This includes database channel if enabled for the employee
        $this->communicationService->sendToModelThroughEnabledChannels(
            $employee,
            'leave_request_approved',
            $data
        );

        // If employee has a user account, ensure database notifications work
        // The sendToModelThroughEnabledChannels should handle this already
        // No need for duplicate database notifications
    }

    /**
     * Send notification for leave request rejection
     */
    public function notifyLeaveRequestRejected(LeaveRequest $leaveRequest, string $rejectionReason): void
    {
        $employee = $leaveRequest->employee;
        $manager = $employee->manager ?? $employee->department?->manager;
        $leaveType = $leaveRequest->leaveType;

        $data = [
            'employee_name' => $employee->full_name,
            'manager_name' => $manager?->full_name ?? 'Manager',
            'leave_type' => $leaveType->name,
            'start_date' => $leaveRequest->start_date->format('M j, Y'),
            'end_date' => $leaveRequest->end_date->format('M j, Y'),
            'duration' => $leaveRequest->duration_in_days,
            'rejection_reason' => $rejectionReason,
        ];

        // Send notification to employee through all enabled channels
        $this->communicationService->sendToModelThroughEnabledChannels(
            $employee,
            'leave_request_rejected',
            $data
        );

        // Create database communication message for the leave request itself
        $this->communicationService->sendToModel(
            $leaveRequest,
            'database',
            'leave_request_rejected',
            $data
        );
    }

    /**
     * Send notification for low leave balance
     */
    public function notifyLowLeaveBalance(Employee $employee, string $leaveTypeName, float $currentBalance, float $threshold = 5): void
    {
        $data = [
            'employee_name' => $employee->full_name,
            'leave_type' => $leaveTypeName,
            'current_balance' => $currentBalance,
            'threshold' => $threshold,
        ];

        // Send notification to employee through all enabled channels
        $this->communicationService->sendToModelThroughEnabledChannels(
            $employee,
            'leave_balance_low',
            $data
        );

        // Create database communication message for the employee
        $this->communicationService->sendToModel(
            $employee,
            'database',
            'leave_balance_low',
            $data
        );
    }

    /**
     * Send notification for leave request cancellation
     */
    public function notifyLeaveRequestCancelled(LeaveRequest $leaveRequest): void
    {
        $employee = $leaveRequest->employee;
        $manager = $employee->manager ?? $employee->department?->manager;
        $leaveType = $leaveRequest->leaveType;

        if ($manager) {
            $data = [
                'employee_name' => $employee->full_name,
                'manager_name' => $manager->full_name,
                'leave_type' => $leaveType->name,
                'start_date' => $leaveRequest->start_date->format('M j, Y'),
                'end_date' => $leaveRequest->end_date->format('M j, Y'),
                'duration' => $leaveRequest->duration_in_days,
            ];

            // Send notification to manager through all enabled channels
            $this->communicationService->sendToModelThroughEnabledChannels(
                $manager,
                'leave_request_cancelled',
                $data
            );

            // Create database communication message for audit trail (attached to leave request)
            $this->communicationService->sendToModel(
                $leaveRequest,
                'database',
                'leave_request_cancelled',
                $data
            );
        }
    }

    /**
     * Check and notify for low leave balances
     */
    public function checkAndNotifyLowBalances(float $threshold = 5): void
    {
        // This would typically be run as a scheduled job
        $employees = Employee::with(['leaveBalances.leaveType'])->get();

        foreach ($employees as $employee) {
            foreach ($employee->leaveBalances as $balance) {
                if ($balance->current_balance <= $threshold && $balance->current_balance > 0) {
                    $this->notifyLowLeaveBalance(
                        $employee,
                        $balance->leaveType->name,
                        $balance->current_balance,
                        $threshold
                    );
                }
            }
        }
    }
}
