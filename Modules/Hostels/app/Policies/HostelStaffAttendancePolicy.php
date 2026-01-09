<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\HostelStaffAttendance;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostelStaffAttendancePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HostelStaffAttendance');
    }

    public function view(AuthUser $authUser, HostelStaffAttendance $hostelStaffAttendance): bool
    {
        return $authUser->can('View:HostelStaffAttendance');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HostelStaffAttendance');
    }

    public function update(AuthUser $authUser, HostelStaffAttendance $hostelStaffAttendance): bool
    {
        return $authUser->can('Update:HostelStaffAttendance');
    }

    public function delete(AuthUser $authUser, HostelStaffAttendance $hostelStaffAttendance): bool
    {
        return $authUser->can('Delete:HostelStaffAttendance');
    }

    public function restore(AuthUser $authUser, HostelStaffAttendance $hostelStaffAttendance): bool
    {
        return $authUser->can('Restore:HostelStaffAttendance');
    }

    public function forceDelete(AuthUser $authUser, HostelStaffAttendance $hostelStaffAttendance): bool
    {
        return $authUser->can('ForceDelete:HostelStaffAttendance');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HostelStaffAttendance');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HostelStaffAttendance');
    }

    public function replicate(AuthUser $authUser, HostelStaffAttendance $hostelStaffAttendance): bool
    {
        return $authUser->can('Replicate:HostelStaffAttendance');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HostelStaffAttendance');
    }

}