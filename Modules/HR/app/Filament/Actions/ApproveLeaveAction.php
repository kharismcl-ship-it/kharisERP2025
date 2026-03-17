<?php

namespace Modules\HR\Filament\Actions;

use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveBalance;
use Modules\HR\Models\LeaveRequest;
use Modules\HR\Services\LeaveApprovalService;

class ApproveLeaveAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'approve_leave';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Approve Leave')
            ->color('success')
            ->icon('heroicon-o-check-circle')
            ->visible(fn ($record) => in_array($record->status, ['pending', 'pending_approval']))
            ->requiresConfirmation()
            ->modalHeading('Approve Leave Request')
            ->modalDescription(function ($record) {
                $requestedDays = $record->start_date->diffInDays($record->end_date) + 1;

                $balance = LeaveBalance::where('employee_id', $record->employee_id)
                    ->where('leave_type_id', $record->leave_type_id)
                    ->first();

                $availableBalance = $balance ? $balance->current_balance : 0;

                if ($availableBalance < $requestedDays) {
                    return "⚠️ **Warning**: Employee only has {$availableBalance} days remaining but requested {$requestedDays} days. \n\nAre you sure you want to approve this leave request? This will result in a negative leave balance.";
                }

                return "Employee has {$availableBalance} days available and requested {$requestedDays} days. \n\nAre you sure you want to approve this leave request?";
            })
            ->modalIcon('heroicon-o-exclamation-triangle')
            ->modalIconColor(function ($record) {
                $requestedDays = $record->start_date->diffInDays($record->end_date) + 1;
                $balance = LeaveBalance::where('employee_id', $record->employee_id)
                    ->where('leave_type_id', $record->leave_type_id)
                    ->first();
                $availableBalance = $balance ? $balance->current_balance : 0;

                return $availableBalance < $requestedDays ? 'warning' : 'success';
            })
            ->action(function ($record) {
                $this->process(function () use ($record) {
                    $requestedDays = $record->start_date->diffInDays($record->end_date) + 1;

                    $balance = LeaveBalance::where('employee_id', $record->employee_id)
                        ->where('leave_type_id', $record->leave_type_id)
                        ->first();

                    $availableBalance = $balance ? $balance->current_balance : 0;

                    $employeeId = $this->getApprovingEmployeeId();

                    if ($record->hasMultiLevelApproval()) {
                        // Handle multi-level approval
                        $this->handleMultiLevelApproval($record, $employeeId);
                    } else {
                        // Handle single-level approval
                        $this->handleSingleLevelApproval($record, $employeeId, $requestedDays);
                    }
                });
            });
    }

    protected function getApprovingEmployeeId(): ?int
    {
        $authUser = Filament::auth()->user();

        if ($authUser && $authUser->employee) {
            return $authUser->employee->id;
        }

        $adminEmployee = Employee::whereHas('jobPosition', function ($query) {
            $query->where('title', 'like', '%manager%')
                ->orWhere('title', 'like', '%admin%');
        })->first();

        return $adminEmployee ? $adminEmployee->id : null;
    }

    protected function handleMultiLevelApproval(LeaveRequest $record, int $approvingEmployeeId): void
    {
        $approvalService = app(LeaveApprovalService::class);
        $currentApproval = $record->currentApprovalRequest;

        if (! $currentApproval || $currentApproval->approver_employee_id !== $approvingEmployeeId) {
            throw new \Exception('You are not authorized to approve this leave request at this level');
        }

        $approvalService->processApproval($currentApproval, 'approved');

        // If this was the final approval, deduct balance and send final notification
        if ($record->status === 'approved') {
            $this->updateLeaveBalance($record);
            $this->sendFinalApprovalNotification($record);
        } else {
            $this->sendIntermediateApprovalNotification($record, $currentApproval);
        }
    }

    protected function handleSingleLevelApproval(LeaveRequest $record, int $approvingEmployeeId, int $requestedDays): void
    {
        DB::transaction(function () use ($record, $approvingEmployeeId) {
            // Update leave request status
            $record->update([
                'status' => 'approved',
                'approved_by_employee_id' => $approvingEmployeeId,
                'approved_at' => now(),
            ]);

            // Deduct from leave balance
            $this->updateLeaveBalance($record);

            // Send notification
            $this->sendApprovalNotification($record);
        });
    }

    protected function updateLeaveBalance(LeaveRequest $record): void
    {
        $requestedDays = $record->start_date->diffInDays($record->end_date) + 1;

        $balance = LeaveBalance::where('employee_id', $record->employee_id)
            ->where('leave_type_id', $record->leave_type_id)
            ->first();

        if ($balance) {
            $balance->decrement('current_balance', $requestedDays);
        } else {
            LeaveBalance::create([
                'employee_id' => $record->employee_id,
                'leave_type_id' => $record->leave_type_id,
                'current_balance' => -$requestedDays,
                'company_id' => $record->company_id,
            ]);
        }
    }

    protected function sendApprovalNotification(LeaveRequest $record): void
    {
        Notification::make()
            ->title('Leave Approved')
            ->body('The leave request has been successfully approved.')
            ->success()
            ->send();
    }

    protected function sendFinalApprovalNotification(LeaveRequest $record): void
    {
        Notification::make()
            ->title('Leave Fully Approved')
            ->body('The leave request has been fully approved through all required levels.')
            ->success()
            ->send();
    }

    protected function sendIntermediateApprovalNotification(LeaveRequest $record, $currentApproval): void
    {
        $nextLevel = $record->getNextApprovalLevel();
        $nextApprover = $nextLevel ? $nextLevel->getApproverForEmployee($record->employee) : null;

        Notification::make()
            ->title('Approval Level Completed')
            ->body('Your approval has been recorded. The request moves to the next approval level.')
            ->success()
            ->send();

        if ($nextApprover) {
            // Send notification to next approver
            // Implementation depends on your notification system
        }
    }
}
