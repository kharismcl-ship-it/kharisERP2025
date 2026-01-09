<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\HostelStaffAssignment;

class HostelStaffAssignmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HostelStaffAssignment');
    }

    public function view(AuthUser $authUser, HostelStaffAssignment $hostelStaffAssignment): bool
    {
        return $authUser->can('View:HostelStaffAssignment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HostelStaffAssignment');
    }

    public function update(AuthUser $authUser, HostelStaffAssignment $hostelStaffAssignment): bool
    {
        return $authUser->can('Update:HostelStaffAssignment');
    }

    public function delete(AuthUser $authUser, HostelStaffAssignment $hostelStaffAssignment): bool
    {
        return $authUser->can('Delete:HostelStaffAssignment');
    }

    public function restore(AuthUser $authUser, HostelStaffAssignment $hostelStaffAssignment): bool
    {
        return $authUser->can('Restore:HostelStaffAssignment');
    }

    public function forceDelete(AuthUser $authUser, HostelStaffAssignment $hostelStaffAssignment): bool
    {
        return $authUser->can('ForceDelete:HostelStaffAssignment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HostelStaffAssignment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HostelStaffAssignment');
    }

    public function replicate(AuthUser $authUser, HostelStaffAssignment $hostelStaffAssignment): bool
    {
        return $authUser->can('Replicate:HostelStaffAssignment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HostelStaffAssignment');
    }
}
