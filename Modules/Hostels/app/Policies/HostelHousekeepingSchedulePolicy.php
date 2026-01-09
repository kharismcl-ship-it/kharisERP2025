<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\HostelHousekeepingSchedule;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostelHousekeepingSchedulePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HostelHousekeepingSchedule');
    }

    public function view(AuthUser $authUser, HostelHousekeepingSchedule $hostelHousekeepingSchedule): bool
    {
        return $authUser->can('View:HostelHousekeepingSchedule');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HostelHousekeepingSchedule');
    }

    public function update(AuthUser $authUser, HostelHousekeepingSchedule $hostelHousekeepingSchedule): bool
    {
        return $authUser->can('Update:HostelHousekeepingSchedule');
    }

    public function delete(AuthUser $authUser, HostelHousekeepingSchedule $hostelHousekeepingSchedule): bool
    {
        return $authUser->can('Delete:HostelHousekeepingSchedule');
    }

    public function restore(AuthUser $authUser, HostelHousekeepingSchedule $hostelHousekeepingSchedule): bool
    {
        return $authUser->can('Restore:HostelHousekeepingSchedule');
    }

    public function forceDelete(AuthUser $authUser, HostelHousekeepingSchedule $hostelHousekeepingSchedule): bool
    {
        return $authUser->can('ForceDelete:HostelHousekeepingSchedule');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HostelHousekeepingSchedule');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HostelHousekeepingSchedule');
    }

    public function replicate(AuthUser $authUser, HostelHousekeepingSchedule $hostelHousekeepingSchedule): bool
    {
        return $authUser->can('Replicate:HostelHousekeepingSchedule');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HostelHousekeepingSchedule');
    }

}