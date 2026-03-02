<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\CropActivity;
use Illuminate\Auth\Access\HandlesAuthorization;

class CropActivityPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CropActivity');
    }

    public function view(AuthUser $authUser, CropActivity $cropActivity): bool
    {
        return $authUser->can('View:CropActivity');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CropActivity');
    }

    public function update(AuthUser $authUser, CropActivity $cropActivity): bool
    {
        return $authUser->can('Update:CropActivity');
    }

    public function delete(AuthUser $authUser, CropActivity $cropActivity): bool
    {
        return $authUser->can('Delete:CropActivity');
    }

    public function restore(AuthUser $authUser, CropActivity $cropActivity): bool
    {
        return $authUser->can('Restore:CropActivity');
    }

    public function forceDelete(AuthUser $authUser, CropActivity $cropActivity): bool
    {
        return $authUser->can('ForceDelete:CropActivity');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CropActivity');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CropActivity');
    }

    public function replicate(AuthUser $authUser, CropActivity $cropActivity): bool
    {
        return $authUser->can('Replicate:CropActivity');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CropActivity');
    }

}