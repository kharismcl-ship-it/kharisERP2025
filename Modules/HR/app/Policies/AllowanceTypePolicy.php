<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\AllowanceType;
use Illuminate\Auth\Access\HandlesAuthorization;

class AllowanceTypePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AllowanceType');
    }

    public function view(AuthUser $authUser, AllowanceType $allowanceType): bool
    {
        return $authUser->can('View:AllowanceType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AllowanceType');
    }

    public function update(AuthUser $authUser, AllowanceType $allowanceType): bool
    {
        return $authUser->can('Update:AllowanceType');
    }

    public function delete(AuthUser $authUser, AllowanceType $allowanceType): bool
    {
        return $authUser->can('Delete:AllowanceType');
    }

    public function restore(AuthUser $authUser, AllowanceType $allowanceType): bool
    {
        return $authUser->can('Restore:AllowanceType');
    }

    public function forceDelete(AuthUser $authUser, AllowanceType $allowanceType): bool
    {
        return $authUser->can('ForceDelete:AllowanceType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AllowanceType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AllowanceType');
    }

    public function replicate(AuthUser $authUser, AllowanceType $allowanceType): bool
    {
        return $authUser->can('Replicate:AllowanceType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AllowanceType');
    }

}