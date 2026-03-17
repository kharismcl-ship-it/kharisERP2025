<?php

declare(strict_types=1);

namespace Modules\Requisition\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Requisition\Models\RequisitionTemplate;
use Illuminate\Auth\Access\HandlesAuthorization;

class RequisitionTemplatePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RequisitionTemplate');
    }

    public function view(AuthUser $authUser, RequisitionTemplate $requisitionTemplate): bool
    {
        return $authUser->can('View:RequisitionTemplate');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RequisitionTemplate');
    }

    public function update(AuthUser $authUser, RequisitionTemplate $requisitionTemplate): bool
    {
        return $authUser->can('Update:RequisitionTemplate');
    }

    public function delete(AuthUser $authUser, RequisitionTemplate $requisitionTemplate): bool
    {
        return $authUser->can('Delete:RequisitionTemplate');
    }

    public function restore(AuthUser $authUser, RequisitionTemplate $requisitionTemplate): bool
    {
        return $authUser->can('Restore:RequisitionTemplate');
    }

    public function forceDelete(AuthUser $authUser, RequisitionTemplate $requisitionTemplate): bool
    {
        return $authUser->can('ForceDelete:RequisitionTemplate');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RequisitionTemplate');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RequisitionTemplate');
    }

    public function replicate(AuthUser $authUser, RequisitionTemplate $requisitionTemplate): bool
    {
        return $authUser->can('Replicate:RequisitionTemplate');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RequisitionTemplate');
    }

}