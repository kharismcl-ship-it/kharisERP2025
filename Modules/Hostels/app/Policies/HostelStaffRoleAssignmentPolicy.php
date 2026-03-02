<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\HostelStaffRoleAssignment;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostelStaffRoleAssignmentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HostelStaffRoleAssignment');
    }

    public function view(AuthUser $authUser, HostelStaffRoleAssignment $hostelStaffRoleAssignment): bool
    {
        return $authUser->can('View:HostelStaffRoleAssignment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HostelStaffRoleAssignment');
    }

    public function update(AuthUser $authUser, HostelStaffRoleAssignment $hostelStaffRoleAssignment): bool
    {
        return $authUser->can('Update:HostelStaffRoleAssignment');
    }

    public function delete(AuthUser $authUser, HostelStaffRoleAssignment $hostelStaffRoleAssignment): bool
    {
        return $authUser->can('Delete:HostelStaffRoleAssignment');
    }

    public function restore(AuthUser $authUser, HostelStaffRoleAssignment $hostelStaffRoleAssignment): bool
    {
        return $authUser->can('Restore:HostelStaffRoleAssignment');
    }

    public function forceDelete(AuthUser $authUser, HostelStaffRoleAssignment $hostelStaffRoleAssignment): bool
    {
        return $authUser->can('ForceDelete:HostelStaffRoleAssignment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HostelStaffRoleAssignment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HostelStaffRoleAssignment');
    }

    public function replicate(AuthUser $authUser, HostelStaffRoleAssignment $hostelStaffRoleAssignment): bool
    {
        return $authUser->can('Replicate:HostelStaffRoleAssignment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HostelStaffRoleAssignment');
    }

}