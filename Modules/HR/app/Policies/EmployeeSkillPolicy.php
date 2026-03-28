<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\EmployeeSkill;

class EmployeeSkillPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EmployeeSkill');
    }

    public function view(AuthUser $authUser, EmployeeSkill $employeeSkill): bool
    {
        return $authUser->can('View:EmployeeSkill');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EmployeeSkill');
    }

    public function update(AuthUser $authUser, EmployeeSkill $employeeSkill): bool
    {
        return $authUser->can('Update:EmployeeSkill');
    }

    public function delete(AuthUser $authUser, EmployeeSkill $employeeSkill): bool
    {
        return $authUser->can('Delete:EmployeeSkill');
    }

    public function restore(AuthUser $authUser, EmployeeSkill $employeeSkill): bool
    {
        return $authUser->can('Restore:EmployeeSkill');
    }

    public function forceDelete(AuthUser $authUser, EmployeeSkill $employeeSkill): bool
    {
        return $authUser->can('ForceDelete:EmployeeSkill');
    }
}