<?php

declare(strict_types=1);

namespace Modules\Requisition\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Requisition\Models\RequisitionWorkflowRule;

class RequisitionWorkflowRulePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RequisitionWorkflowRule');
    }

    public function view(AuthUser $authUser, RequisitionWorkflowRule $rule): bool
    {
        return $authUser->can('View:RequisitionWorkflowRule');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RequisitionWorkflowRule');
    }

    public function update(AuthUser $authUser, RequisitionWorkflowRule $rule): bool
    {
        return $authUser->can('Update:RequisitionWorkflowRule');
    }

    public function delete(AuthUser $authUser, RequisitionWorkflowRule $rule): bool
    {
        return $authUser->can('Delete:RequisitionWorkflowRule');
    }

    public function restore(AuthUser $authUser, RequisitionWorkflowRule $rule): bool
    {
        return $authUser->can('Restore:RequisitionWorkflowRule');
    }

    public function forceDelete(AuthUser $authUser, RequisitionWorkflowRule $rule): bool
    {
        return $authUser->can('ForceDelete:RequisitionWorkflowRule');
    }
}