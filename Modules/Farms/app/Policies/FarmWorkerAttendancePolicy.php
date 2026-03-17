<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\FarmWorkerAttendance;
use Illuminate\Auth\Access\HandlesAuthorization;

class FarmWorkerAttendancePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FarmWorkerAttendance');
    }

    public function view(AuthUser $authUser, FarmWorkerAttendance $farmWorkerAttendance): bool
    {
        return $authUser->can('View:FarmWorkerAttendance');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FarmWorkerAttendance');
    }

    public function update(AuthUser $authUser, FarmWorkerAttendance $farmWorkerAttendance): bool
    {
        return $authUser->can('Update:FarmWorkerAttendance');
    }

    public function delete(AuthUser $authUser, FarmWorkerAttendance $farmWorkerAttendance): bool
    {
        return $authUser->can('Delete:FarmWorkerAttendance');
    }

    public function restore(AuthUser $authUser, FarmWorkerAttendance $farmWorkerAttendance): bool
    {
        return $authUser->can('Restore:FarmWorkerAttendance');
    }

    public function forceDelete(AuthUser $authUser, FarmWorkerAttendance $farmWorkerAttendance): bool
    {
        return $authUser->can('ForceDelete:FarmWorkerAttendance');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FarmWorkerAttendance');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FarmWorkerAttendance');
    }

    public function replicate(AuthUser $authUser, FarmWorkerAttendance $farmWorkerAttendance): bool
    {
        return $authUser->can('Replicate:FarmWorkerAttendance');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FarmWorkerAttendance');
    }

}