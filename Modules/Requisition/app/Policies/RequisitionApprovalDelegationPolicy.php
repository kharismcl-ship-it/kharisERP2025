<?php

declare(strict_types=1);

namespace Modules\Requisition\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Requisition\Models\RequisitionApprovalDelegation;

class RequisitionApprovalDelegationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RequisitionApprovalDelegation');
    }

    public function view(AuthUser $authUser, RequisitionApprovalDelegation $delegation): bool
    {
        return $authUser->can('View:RequisitionApprovalDelegation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RequisitionApprovalDelegation');
    }

    public function update(AuthUser $authUser, RequisitionApprovalDelegation $delegation): bool
    {
        return $authUser->can('Update:RequisitionApprovalDelegation');
    }

    public function delete(AuthUser $authUser, RequisitionApprovalDelegation $delegation): bool
    {
        return $authUser->can('Delete:RequisitionApprovalDelegation');
    }
}