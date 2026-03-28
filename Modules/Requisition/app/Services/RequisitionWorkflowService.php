<?php

declare(strict_types=1);

namespace Modules\Requisition\Services;

use Modules\Requisition\Models\Requisition;
use Modules\Requisition\Models\RequisitionActivity;
use Modules\Requisition\Models\RequisitionApprover;
use Modules\Requisition\Models\RequisitionWorkflowRule;

class RequisitionWorkflowService
{
    /**
     * Apply active workflow rules to a newly submitted requisition.
     * Creates RequisitionApprover records for each matching rule, skipping
     * duplicates by employee_id.
     */
    public function applyWorkflowRules(Requisition $requisition): void
    {
        $matchingRules = RequisitionWorkflowRule::matchingRules($requisition);

        if ($matchingRules->isEmpty()) {
            return;
        }

        // Determine starting order after existing approvers
        $maxOrder = RequisitionApprover::where('requisition_id', $requisition->id)
            ->max('order') ?? 0;

        // Collect already-assigned employee IDs to avoid duplicates
        $existingEmployeeIds = RequisitionApprover::where('requisition_id', $requisition->id)
            ->pluck('employee_id')
            ->toArray();

        $addedCount = 0;

        foreach ($matchingRules as $rule) {
            $employeeIds = $rule->approver_employee_ids ?? [];
            $roles       = $rule->approver_roles ?? [];

            foreach ($employeeIds as $index => $employeeId) {
                if (in_array($employeeId, $existingEmployeeIds, true)) {
                    continue;
                }

                $role = $roles[$index] ?? 'reviewer';
                $maxOrder++;

                RequisitionApprover::create([
                    'requisition_id' => $requisition->id,
                    'employee_id'    => $employeeId,
                    'role'           => $role,
                    'order'          => $maxOrder,
                    'is_active'      => true,
                    'decision'       => 'pending',
                ]);

                $existingEmployeeIds[] = $employeeId;
                $addedCount++;
            }
        }

        if ($addedCount > 0) {
            RequisitionActivity::log(
                $requisition,
                'workflow_rules_applied',
                "Workflow rules applied: {$addedCount} approver(s) auto-assigned.",
            );
        }
    }
}