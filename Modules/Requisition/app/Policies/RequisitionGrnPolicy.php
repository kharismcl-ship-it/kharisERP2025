<?php

declare(strict_types=1);

namespace Modules\Requisition\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Requisition\Models\RequisitionGrn;

class RequisitionGrnPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RequisitionGrn');
    }

    public function view(AuthUser $authUser, RequisitionGrn $grn): bool
    {
        return $authUser->can('View:RequisitionGrn');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RequisitionGrn');
    }

    public function update(AuthUser $authUser, RequisitionGrn $grn): bool
    {
        return $authUser->can('Update:RequisitionGrn');
    }

    public function delete(AuthUser $authUser, RequisitionGrn $grn): bool
    {
        return $authUser->can('Delete:RequisitionGrn');
    }
}