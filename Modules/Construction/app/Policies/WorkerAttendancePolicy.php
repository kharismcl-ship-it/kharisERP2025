<?php

declare(strict_types=1);

namespace Modules\Construction\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Construction\Models\WorkerAttendance;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkerAttendancePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:WorkerAttendance');
    }

    public function view(AuthUser $authUser, WorkerAttendance $workerAttendance): bool
    {
        return $authUser->can('View:WorkerAttendance');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:WorkerAttendance');
    }

    public function update(AuthUser $authUser, WorkerAttendance $workerAttendance): bool
    {
        return $authUser->can('Update:WorkerAttendance');
    }

    public function delete(AuthUser $authUser, WorkerAttendance $workerAttendance): bool
    {
        return $authUser->can('Delete:WorkerAttendance');
    }

    public function restore(AuthUser $authUser, WorkerAttendance $workerAttendance): bool
    {
        return $authUser->can('Restore:WorkerAttendance');
    }

    public function forceDelete(AuthUser $authUser, WorkerAttendance $workerAttendance): bool
    {
        return $authUser->can('ForceDelete:WorkerAttendance');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:WorkerAttendance');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:WorkerAttendance');
    }

    public function replicate(AuthUser $authUser, WorkerAttendance $workerAttendance): bool
    {
        return $authUser->can('Replicate:WorkerAttendance');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:WorkerAttendance');
    }

}