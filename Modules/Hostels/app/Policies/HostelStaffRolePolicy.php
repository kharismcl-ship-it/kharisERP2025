<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\HostelStaffRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostelStaffRolePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HostelStaffRole');
    }

    public function view(AuthUser $authUser, HostelStaffRole $hostelStaffRole): bool
    {
        return $authUser->can('View:HostelStaffRole');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HostelStaffRole');
    }

    public function update(AuthUser $authUser, HostelStaffRole $hostelStaffRole): bool
    {
        return $authUser->can('Update:HostelStaffRole');
    }

    public function delete(AuthUser $authUser, HostelStaffRole $hostelStaffRole): bool
    {
        return $authUser->can('Delete:HostelStaffRole');
    }

    public function restore(AuthUser $authUser, HostelStaffRole $hostelStaffRole): bool
    {
        return $authUser->can('Restore:HostelStaffRole');
    }

    public function forceDelete(AuthUser $authUser, HostelStaffRole $hostelStaffRole): bool
    {
        return $authUser->can('ForceDelete:HostelStaffRole');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HostelStaffRole');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HostelStaffRole');
    }

    public function replicate(AuthUser $authUser, HostelStaffRole $hostelStaffRole): bool
    {
        return $authUser->can('Replicate:HostelStaffRole');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HostelStaffRole');
    }

}