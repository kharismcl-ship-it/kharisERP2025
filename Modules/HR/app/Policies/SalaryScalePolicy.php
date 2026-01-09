<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\SalaryScale;

class SalaryScalePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SalaryScale');
    }

    public function view(AuthUser $authUser, SalaryScale $salaryScale): bool
    {
        return $authUser->can('View:SalaryScale');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SalaryScale');
    }

    public function update(AuthUser $authUser, SalaryScale $salaryScale): bool
    {
        return $authUser->can('Update:SalaryScale');
    }

    public function delete(AuthUser $authUser, SalaryScale $salaryScale): bool
    {
        return $authUser->can('Delete:SalaryScale');
    }

    public function restore(AuthUser $authUser, SalaryScale $salaryScale): bool
    {
        return $authUser->can('Restore:SalaryScale');
    }

    public function forceDelete(AuthUser $authUser, SalaryScale $salaryScale): bool
    {
        return $authUser->can('ForceDelete:SalaryScale');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SalaryScale');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SalaryScale');
    }

    public function replicate(AuthUser $authUser, SalaryScale $salaryScale): bool
    {
        return $authUser->can('Replicate:SalaryScale');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SalaryScale');
    }
}
