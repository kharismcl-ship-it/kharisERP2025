<?php

namespace Modules\Requisition\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Requisition\Models\RequisitionActivity;
use Modules\Requisition\Models\RequisitionApprover;

class ApprovalController extends Controller
{
    /**
     * GET /requisition-approval/{token}/approve
     */
    public function approve(string $token): View|Response
    {
        $approver = RequisitionApprover::where('approval_token', $token)->first();

        if (! $approver) {
            return view('requisition::approval.expired');
        }

        if (! $approver->isTokenValid()) {
            return view('requisition::approval.expired');
        }

        if ($approver->decision !== 'pending') {
            return view('requisition::approval.already_decided', [
                'decision' => $approver->decision,
            ]);
        }

        $approver->update([
            'decision'       => 'approved',
            'decided_at'     => now(),
            'approval_token' => null,
        ]);

        $requisition = $approver->requisition;

        if ($requisition) {
            RequisitionActivity::log(
                $requisition,
                'approver_decision',
                "Approved via email link by {$approver->employee?->full_name}.",
            );

            // Check if all approvers have decided
            $pendingCount = $requisition->approvers()
                ->where('decision', 'pending')
                ->where('is_active', true)
                ->count();

            if ($pendingCount === 0) {
                $requisition->update([
                    'status'      => 'approved',
                    'approved_at' => now(),
                ]);
            }
        }

        return view('requisition::approval.success', [
            'action'    => 'approved',
            'reference' => $requisition?->reference ?? '—',
        ]);
    }

    /**
     * GET /requisition-approval/{token}/reject
     */
    public function reject(string $token): View|Response
    {
        $approver = RequisitionApprover::where('approval_token', $token)->first();

        if (! $approver) {
            return view('requisition::approval.expired');
        }

        if (! $approver->isTokenValid()) {
            return view('requisition::approval.expired');
        }

        if ($approver->decision !== 'pending') {
            return view('requisition::approval.already_decided', [
                'decision' => $approver->decision,
            ]);
        }

        $approver->update([
            'decision'       => 'rejected',
            'decided_at'     => now(),
            'approval_token' => null,
            'comment'        => 'Rejected via email link.',
        ]);

        $requisition = $approver->requisition;

        if ($requisition) {
            RequisitionActivity::log(
                $requisition,
                'approver_decision',
                "Rejected via email link by {$approver->employee?->full_name}.",
            );

            $requisition->update([
                'status'           => 'rejected',
                'rejection_reason' => 'Rejected by approver via email.',
            ]);
        }

        return view('requisition::approval.success', [
            'action'    => 'rejected',
            'reference' => $requisition?->reference ?? '—',
        ]);
    }
}