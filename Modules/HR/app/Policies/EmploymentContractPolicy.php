<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\EmploymentContract;

class EmploymentContractPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:EmploymentContract');
    }

    public function view(AuthUser $authUser, EmploymentContract $employmentContract): bool
    {
        return $authUser->can('View:EmploymentContract');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:EmploymentContract');
    }

    public function update(AuthUser $authUser, EmploymentContract $employmentContract): bool
    {
        return $authUser->can('Update:EmploymentContract');
    }

    public function delete(AuthUser $authUser, EmploymentContract $employmentContract): bool
    {
        return $authUser->can('Delete:EmploymentContract');
    }

    public function restore(AuthUser $authUser, EmploymentContract $employmentContract): bool
    {
        return $authUser->can('Restore:EmploymentContract');
    }

    public function forceDelete(AuthUser $authUser, EmploymentContract $employmentContract): bool
    {
        return $authUser->can('ForceDelete:EmploymentContract');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:EmploymentContract');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:EmploymentContract');
    }

    public function replicate(AuthUser $authUser, EmploymentContract $employmentContract): bool
    {
        return $authUser->can('Replicate:EmploymentContract');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:EmploymentContract');
    }
}
