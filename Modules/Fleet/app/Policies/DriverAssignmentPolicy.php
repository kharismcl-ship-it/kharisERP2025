<?php

declare(strict_types=1);

namespace Modules\Fleet\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Fleet\Models\DriverAssignment;
use Illuminate\Auth\Access\HandlesAuthorization;

class DriverAssignmentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DriverAssignment');
    }

    public function view(AuthUser $authUser, DriverAssignment $driverAssignment): bool
    {
        return $authUser->can('View:DriverAssignment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DriverAssignment');
    }

    public function update(AuthUser $authUser, DriverAssignment $driverAssignment): bool
    {
        return $authUser->can('Update:DriverAssignment');
    }

    public function delete(AuthUser $authUser, DriverAssignment $driverAssignment): bool
    {
        return $authUser->can('Delete:DriverAssignment');
    }

    public function restore(AuthUser $authUser, DriverAssignment $driverAssignment): bool
    {
        return $authUser->can('Restore:DriverAssignment');
    }

    public function forceDelete(AuthUser $authUser, DriverAssignment $driverAssignment): bool
    {
        return $authUser->can('ForceDelete:DriverAssignment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DriverAssignment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DriverAssignment');
    }

    public function replicate(AuthUser $authUser, DriverAssignment $driverAssignment): bool
    {
        return $authUser->can('Replicate:DriverAssignment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DriverAssignment');
    }

}