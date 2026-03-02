<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\CropCycle;
use Illuminate\Auth\Access\HandlesAuthorization;

class CropCyclePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CropCycle');
    }

    public function view(AuthUser $authUser, CropCycle $cropCycle): bool
    {
        return $authUser->can('View:CropCycle');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CropCycle');
    }

    public function update(AuthUser $authUser, CropCycle $cropCycle): bool
    {
        return $authUser->can('Update:CropCycle');
    }

    public function delete(AuthUser $authUser, CropCycle $cropCycle): bool
    {
        return $authUser->can('Delete:CropCycle');
    }

    public function restore(AuthUser $authUser, CropCycle $cropCycle): bool
    {
        return $authUser->can('Restore:CropCycle');
    }

    public function forceDelete(AuthUser $authUser, CropCycle $cropCycle): bool
    {
        return $authUser->can('ForceDelete:CropCycle');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CropCycle');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CropCycle');
    }

    public function replicate(AuthUser $authUser, CropCycle $cropCycle): bool
    {
        return $authUser->can('Replicate:CropCycle');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CropCycle');
    }

}