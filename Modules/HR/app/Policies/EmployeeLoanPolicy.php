<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\EmployeeLoan;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeeLoanPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EmployeeLoan');
    }

    public function view(AuthUser $authUser, EmployeeLoan $employeeLoan): bool
    {
        return $authUser->can('View:EmployeeLoan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EmployeeLoan');
    }

    public function update(AuthUser $authUser, EmployeeLoan $employeeLoan): bool
    {
        return $authUser->can('Update:EmployeeLoan');
    }

    public function delete(AuthUser $authUser, EmployeeLoan $employeeLoan): bool
    {
        return $authUser->can('Delete:EmployeeLoan');
    }

    public function restore(AuthUser $authUser, EmployeeLoan $employeeLoan): bool
    {
        return $authUser->can('Restore:EmployeeLoan');
    }

    public function forceDelete(AuthUser $authUser, EmployeeLoan $employeeLoan): bool
    {
        return $authUser->can('ForceDelete:EmployeeLoan');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EmployeeLoan');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EmployeeLoan');
    }

    public function replicate(AuthUser $authUser, EmployeeLoan $employeeLoan): bool
    {
        return $authUser->can('Replicate:EmployeeLoan');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EmployeeLoan');
    }

}