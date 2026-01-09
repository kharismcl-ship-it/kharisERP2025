<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\EmployeeCompanyAssignment;

class EmployeeCompanyAssignmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EmployeeCompanyAssignment');
    }

    public function view(AuthUser $authUser, EmployeeCompanyAssignment $employeeCompanyAssignment): bool
    {
        return $authUser->can('View:EmployeeCompanyAssignment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EmployeeCompanyAssignment');
    }

    public function update(AuthUser $authUser, EmployeeCompanyAssignment $employeeCompanyAssignment): bool
    {
        return $authUser->can('Update:EmployeeCompanyAssignment');
    }

    public function delete(AuthUser $authUser, EmployeeCompanyAssignment $employeeCompanyAssignment): bool
    {
        return $authUser->can('Delete:EmployeeCompanyAssignment');
    }

    public function restore(AuthUser $authUser, EmployeeCompanyAssignment $employeeCompanyAssignment): bool
    {
        return $authUser->can('Restore:EmployeeCompanyAssignment');
    }

    public function forceDelete(AuthUser $authUser, EmployeeCompanyAssignment $employeeCompanyAssignment): bool
    {
        return $authUser->can('ForceDelete:EmployeeCompanyAssignment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EmployeeCompanyAssignment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EmployeeCompanyAssignment');
    }

    public function replicate(AuthUser $authUser, EmployeeCompanyAssignment $employeeCompanyAssignment): bool
    {
        return $authUser->can('Replicate:EmployeeCompanyAssignment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EmployeeCompanyAssignment');
    }
}
