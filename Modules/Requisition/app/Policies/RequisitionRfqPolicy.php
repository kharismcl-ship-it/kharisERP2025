<?php

declare(strict_types=1);

namespace Modules\Requisition\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Requisition\Models\RequisitionRfq;

class RequisitionRfqPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RequisitionRfq');
    }

    public function view(AuthUser $authUser, RequisitionRfq $rfq): bool
    {
        return $authUser->can('View:RequisitionRfq');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RequisitionRfq');
    }

    public function update(AuthUser $authUser, RequisitionRfq $rfq): bool
    {
        return $authUser->can('Update:RequisitionRfq');
    }

    public function delete(AuthUser $authUser, RequisitionRfq $rfq): bool
    {
        return $authUser->can('Delete:RequisitionRfq');
    }
}