<?php

declare(strict_types=1);

namespace Modules\Requisition\Services;

use Modules\Requisition\Models\Requisition;
use Modules\Requisition\Models\RequisitionApprover;

class RequisitionPolicyEnforcementService
{
    /**
     * Check whether submitting this requisition would exceed the cost centre budget.
     *
     * Returns null if the requisition is within budget (or no budget is set).
     * Returns an error message string if the budget would be exceeded.
     */
    public function checkBudget(Requisition $req): ?string
    {
        if (! $req->cost_centre_id || ! $req->total_estimated_cost) {
            return null;
        }

        $budgetAmount = $req->costCentre?->budget_amount;

        if (! $budgetAmount) {
            return null;
        }

        // Sum all active (non-rejected, non-closed, non-cancelled) requisitions
        // against this cost centre, excluding the current one
        $committed = Requisition::where('cost_centre_id', $req->cost_centre_id)
            ->whereNotIn('status', ['rejected', 'closed', 'cancelled'])
            ->where('id', '!=', $req->id)
            ->sum('total_estimated_cost');

        $available = (float) $budgetAmount - (float) $committed;
        $amount    = (float) $req->total_estimated_cost;

        if ($amount > $available) {
            $over = number_format($amount - $available, 2);
            $avail = number_format($available, 2);
            $centre = $req->costCentre->name ?? 'selected cost centre';
            return "This requisition (GHS {$amount}) exceeds the available budget for {$centre}. Available: GHS {$avail}. Over by: GHS {$over}.";
        }

        return null;
    }

    /**
     * Check segregation of duties: the requester must not also be an approver.
     *
     * Returns null if no conflict.
     * Returns an error message string if the requester is also an approver.
     */
    public function checkSegregationOfDuties(Requisition $req): ?string
    {
        if (! $req->requester_employee_id) {
            return null;
        }

        $isApprover = RequisitionApprover::where('requisition_id', $req->id)
            ->where('employee_id', $req->requester_employee_id)
            ->exists();

        if ($isApprover) {
            $name = $req->requesterEmployee?->full_name ?? 'The requester';
            return "Segregation of duties violation: {$name} is listed both as the requester and as an approver. Please remove them from the approvers list before submitting.";
        }

        return null;
    }
}