<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\EmployeeSalary;

class EmployeeSalaryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EmployeeSalary');
    }

    public function view(AuthUser $authUser, EmployeeSalary $employeeSalary): bool
    {
        return $authUser->can('View:EmployeeSalary');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EmployeeSalary');
    }

    public function update(AuthUser $authUser, EmployeeSalary $employeeSalary): bool
    {
        return $authUser->can('Update:EmployeeSalary');
    }

    public function delete(AuthUser $authUser, EmployeeSalary $employeeSalary): bool
    {
        return $authUser->can('Delete:EmployeeSalary');
    }

    public function restore(AuthUser $authUser, EmployeeSalary $employeeSalary): bool
    {
        return $authUser->can('Restore:EmployeeSalary');
    }

    public function forceDelete(AuthUser $authUser, EmployeeSalary $employeeSalary): bool
    {
        return $authUser->can('ForceDelete:EmployeeSalary');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EmployeeSalary');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EmployeeSalary');
    }

    public function replicate(AuthUser $authUser, EmployeeSalary $employeeSalary): bool
    {
        return $authUser->can('Replicate:EmployeeSalary');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EmployeeSalary');
    }
}
