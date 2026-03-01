<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\LeaveApprovalWorkflow;

class LeaveApprovalWorkflowPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LeaveApprovalWorkflow');
    }

    public function view(AuthUser $authUser, LeaveApprovalWorkflow $leaveApprovalWorkflow): bool
    {
        return $authUser->can('View:LeaveApprovalWorkflow');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LeaveApprovalWorkflow');
    }

    public function update(AuthUser $authUser, LeaveApprovalWorkflow $leaveApprovalWorkflow): bool
    {
        return $authUser->can('Update:LeaveApprovalWorkflow');
    }

    public function delete(AuthUser $authUser, LeaveApprovalWorkflow $leaveApprovalWorkflow): bool
    {
        return $authUser->can('Delete:LeaveApprovalWorkflow');
    }

    public function restore(AuthUser $authUser, LeaveApprovalWorkflow $leaveApprovalWorkflow): bool
    {
        return $authUser->can('Restore:LeaveApprovalWorkflow');
    }

    public function forceDelete(AuthUser $authUser, LeaveApprovalWorkflow $leaveApprovalWorkflow): bool
    {
        return $authUser->can('ForceDelete:LeaveApprovalWorkflow');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LeaveApprovalWorkflow');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LeaveApprovalWorkflow');
    }

    public function replicate(AuthUser $authUser, LeaveApprovalWorkflow $leaveApprovalWorkflow): bool
    {
        return $authUser->can('Replicate:LeaveApprovalWorkflow');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LeaveApprovalWorkflow');
    }
}
