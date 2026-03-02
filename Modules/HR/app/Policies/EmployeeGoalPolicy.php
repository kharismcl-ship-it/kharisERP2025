<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\EmployeeGoal;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeeGoalPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EmployeeGoal');
    }

    public function view(AuthUser $authUser, EmployeeGoal $employeeGoal): bool
    {
        return $authUser->can('View:EmployeeGoal');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EmployeeGoal');
    }

    public function update(AuthUser $authUser, EmployeeGoal $employeeGoal): bool
    {
        return $authUser->can('Update:EmployeeGoal');
    }

    public function delete(AuthUser $authUser, EmployeeGoal $employeeGoal): bool
    {
        return $authUser->can('Delete:EmployeeGoal');
    }

    public function restore(AuthUser $authUser, EmployeeGoal $employeeGoal): bool
    {
        return $authUser->can('Restore:EmployeeGoal');
    }

    public function forceDelete(AuthUser $authUser, EmployeeGoal $employeeGoal): bool
    {
        return $authUser->can('ForceDelete:EmployeeGoal');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EmployeeGoal');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EmployeeGoal');
    }

    public function replicate(AuthUser $authUser, EmployeeGoal $employeeGoal): bool
    {
        return $authUser->can('Replicate:EmployeeGoal');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EmployeeGoal');
    }

}