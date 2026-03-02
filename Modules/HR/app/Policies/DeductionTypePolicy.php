<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\DeductionType;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeductionTypePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DeductionType');
    }

    public function view(AuthUser $authUser, DeductionType $deductionType): bool
    {
        return $authUser->can('View:DeductionType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DeductionType');
    }

    public function update(AuthUser $authUser, DeductionType $deductionType): bool
    {
        return $authUser->can('Update:DeductionType');
    }

    public function delete(AuthUser $authUser, DeductionType $deductionType): bool
    {
        return $authUser->can('Delete:DeductionType');
    }

    public function restore(AuthUser $authUser, DeductionType $deductionType): bool
    {
        return $authUser->can('Restore:DeductionType');
    }

    public function forceDelete(AuthUser $authUser, DeductionType $deductionType): bool
    {
        return $authUser->can('ForceDelete:DeductionType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DeductionType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DeductionType');
    }

    public function replicate(AuthUser $authUser, DeductionType $deductionType): bool
    {
        return $authUser->can('Replicate:DeductionType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DeductionType');
    }

}