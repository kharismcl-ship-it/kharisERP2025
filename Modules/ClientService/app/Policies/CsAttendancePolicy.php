<?php

declare(strict_types=1);

namespace Modules\ClientService\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ClientService\Models\CsAttendance;
use Illuminate\Auth\Access\HandlesAuthorization;

class CsAttendancePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CsAttendance');
    }

    public function view(AuthUser $authUser, CsAttendance $csAttendance): bool
    {
        return $authUser->can('View:CsAttendance');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CsAttendance');
    }

    public function update(AuthUser $authUser, CsAttendance $csAttendance): bool
    {
        return $authUser->can('Update:CsAttendance');
    }

    public function delete(AuthUser $authUser, CsAttendance $csAttendance): bool
    {
        return $authUser->can('Delete:CsAttendance');
    }

    public function restore(AuthUser $authUser, CsAttendance $csAttendance): bool
    {
        return $authUser->can('Restore:CsAttendance');
    }

    public function forceDelete(AuthUser $authUser, CsAttendance $csAttendance): bool
    {
        return $authUser->can('ForceDelete:CsAttendance');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CsAttendance');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CsAttendance');
    }

    public function replicate(AuthUser $authUser, CsAttendance $csAttendance): bool
    {
        return $authUser->can('Replicate:CsAttendance');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CsAttendance');
    }

}