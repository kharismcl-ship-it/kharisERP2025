<?php

namespace Modules\HR\Services;

use Illuminate\Support\Facades\DB;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveApprovalDelegation;
use Modules\HR\Models\LeaveApprovalLevel;
use Modules\HR\Models\LeaveApprovalRequest;
use Modules\HR\Models\LeaveApprovalWorkflow;
use Modules\HR\Models\LeaveRequest;

class LeaveApprovalService
{
    /**
     * Get the appropriate approval workflow for a leave request.
     */
    public function getWorkflowForLeaveRequest(LeaveRequest $leaveRequest): ?LeaveApprovalWorkflow
    {
        // First check if there's a workflow specific to the employee's department
        $departmentWorkflow = LeaveApprovalWorkflow::where('company_id', $leaveRequest->company_id)
            ->active()
            ->whereHas('levels', function ($query) use ($leaveRequest) {
                $query->where('approver_department_id', $leaveRequest->employee->department_id);
            })
            ->first();

        if ($departmentWorkflow) {
            return $departmentWorkflow;
        }

        // Fall back to company default workflow
        return LeaveApprovalWorkflow::where('company_id', $leaveRequest->company_id)
            ->active()
            ->first();
    }

    /**
     * Initialize multi-level approval process for a leave request.
     */
    public function initializeApprovalProcess(LeaveRequest $leaveRequest): void
    {
        $workflow = $this->getWorkflowForLeaveRequest($leaveRequest);

        if (! $workflow) {
            // No workflow configured, use single-level approval
            $this->createSingleLevelApproval($leaveRequest);

            return;
        }

        DB::transaction(function () use ($leaveRequest, $workflow) {
            foreach ($workflow->activeLevels as $level) {
                $approver = $this->resolveApproverForLevel($level, $leaveRequest->employee);

                if ($approver) {
                    LeaveApprovalRequest::create([
                        'leave_request_id' => $leaveRequest->id,
                        'approval_level_id' => $level->id,
                        'approver_employee_id' => $approver->id,
                        'status' => 'pending',
                    ]);
                }
            }

            // Update leave request status to reflect pending approvals
            $leaveRequest->update(['status' => 'pending_approval']);
        });
    }

    /**
     * Resolve the actual approver for an approval level, considering delegations.
     */
    public function resolveApproverForLevel(LeaveApprovalLevel $level, Employee $employee): ?Employee
    {
        $approver = $level->getApproverForEmployee($employee);

        if (! $approver) {
            return null;
        }

        // Check for active delegations
        $delegation = LeaveApprovalDelegation::getActiveDelegationForApprover($approver->id);

        if ($delegation) {
            return $delegation->delegate;
        }

        return $approver;
    }

    /**
     * Process approval for a leave request at the current level.
     */
    public function processApproval(LeaveApprovalRequest $approvalRequest, string $status, ?string $comments = null): void
    {
        DB::transaction(function () use ($approvalRequest, $status, $comments) {
            if ($status === 'approved') {
                $approvalRequest->markAsApproved($comments);
                $this->checkIfFullyApproved($approvalRequest->leaveRequest);
            } else {
                $approvalRequest->markAsRejected($comments);
                $approvalRequest->leaveRequest->update(['status' => 'rejected']);
            }
        });
    }

    /**
     * Check if all required approvals have been obtained.
     */
    protected function checkIfFullyApproved(LeaveRequest $leaveRequest): void
    {
        $workflow = $this->getWorkflowForLeaveRequest($leaveRequest);

        if (! $workflow) {
            // Single level approval - mark as approved
            $leaveRequest->update(['status' => 'approved']);

            return;
        }

        $approvedLevels = $leaveRequest->approvalRequests()->where('status', 'approved')->count();
        $requiredLevels = $workflow->levels()->where('is_required', true)->count();

        if ($approvedLevels >= $requiredLevels) {
            $leaveRequest->update(['status' => 'approved']);
        } elseif ($leaveRequest->requiresAdditionalApprovals()) {
            // Move to next approval level
            $nextLevel = $leaveRequest->getNextApprovalLevel();
            if ($nextLevel) {
                $approver = $this->resolveApproverForLevel($nextLevel, $leaveRequest->employee);
                if ($approver) {
                    LeaveApprovalRequest::create([
                        'leave_request_id' => $leaveRequest->id,
                        'approval_level_id' => $nextLevel->id,
                        'approver_employee_id' => $approver->id,
                        'status' => 'pending',
                    ]);
                }
            }
        }
    }

    /**
     * Create single-level approval request (fallback when no workflow configured).
     */
    protected function createSingleLevelApproval(LeaveRequest $leaveRequest): void
    {
        $approver = $this->getDefaultApprover($leaveRequest);

        if ($approver) {
            LeaveApprovalRequest::create([
                'leave_request_id' => $leaveRequest->id,
                'approval_level_id' => null, // No level for single approval
                'approver_employee_id' => $approver->id,
                'status' => 'pending',
            ]);

            $leaveRequest->update(['status' => 'pending_approval']);
        } else {
            // No approver found, auto-approve
            $leaveRequest->update(['status' => 'approved']);
        }
    }

    /**
     * Get default approver for single-level approval.
     */
    protected function getDefaultApprover(LeaveRequest $leaveRequest): ?Employee
    {
        // Try manager first
        if ($leaveRequest->employee->manager) {
            return $leaveRequest->employee->manager;
        }

        // Then try department head
        if ($leaveRequest->employee->department && $leaveRequest->employee->department->head) {
            return $leaveRequest->employee->department->head;
        }

        // Finally try HR/admin
        return Employee::whereHas('jobPosition', function ($query) {
            $query->where('title', 'like', '%HR%')
                ->orWhere('title', 'like', '%admin%')
                ->orWhere('title', 'like', '%manager%');
        })->first();
    }

    /**
     * Get pending approval requests for an employee.
     */
    public function getPendingApprovalsForEmployee(Employee $employee)
    {
        return LeaveApprovalRequest::where('approver_employee_id', $employee->id)
            ->where('status', 'pending')
            ->with(['leaveRequest.employee', 'leaveRequest.leaveType'])
            ->get();
    }

    /**
     * Check if an employee can approve a specific leave request.
     */
    public function canApproveLeave(Employee $employee, LeaveRequest $leaveRequest): bool
    {
        $pendingApproval = $leaveRequest->currentApprovalRequest;

        if (! $pendingApproval) {
            return false;
        }

        return $pendingApproval->approver_employee_id === $employee->id;
    }
}
