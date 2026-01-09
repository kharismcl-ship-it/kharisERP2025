<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\HostelUtilityCharge;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostelUtilityChargePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HostelUtilityCharge');
    }

    public function view(AuthUser $authUser, HostelUtilityCharge $hostelUtilityCharge): bool
    {
        return $authUser->can('View:HostelUtilityCharge');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HostelUtilityCharge');
    }

    public function update(AuthUser $authUser, HostelUtilityCharge $hostelUtilityCharge): bool
    {
        return $authUser->can('Update:HostelUtilityCharge');
    }

    public function delete(AuthUser $authUser, HostelUtilityCharge $hostelUtilityCharge): bool
    {
        return $authUser->can('Delete:HostelUtilityCharge');
    }

    public function restore(AuthUser $authUser, HostelUtilityCharge $hostelUtilityCharge): bool
    {
        return $authUser->can('Restore:HostelUtilityCharge');
    }

    public function forceDelete(AuthUser $authUser, HostelUtilityCharge $hostelUtilityCharge): bool
    {
        return $authUser->can('ForceDelete:HostelUtilityCharge');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HostelUtilityCharge');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HostelUtilityCharge');
    }

    public function replicate(AuthUser $authUser, HostelUtilityCharge $hostelUtilityCharge): bool
    {
        return $authUser->can('Replicate:HostelUtilityCharge');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HostelUtilityCharge');
    }

}