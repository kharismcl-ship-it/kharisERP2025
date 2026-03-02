<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\CropVariety;
use Illuminate\Auth\Access\HandlesAuthorization;

class CropVarietyPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CropVariety');
    }

    public function view(AuthUser $authUser, CropVariety $cropVariety): bool
    {
        return $authUser->can('View:CropVariety');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CropVariety');
    }

    public function update(AuthUser $authUser, CropVariety $cropVariety): bool
    {
        return $authUser->can('Update:CropVariety');
    }

    public function delete(AuthUser $authUser, CropVariety $cropVariety): bool
    {
        return $authUser->can('Delete:CropVariety');
    }

    public function restore(AuthUser $authUser, CropVariety $cropVariety): bool
    {
        return $authUser->can('Restore:CropVariety');
    }

    public function forceDelete(AuthUser $authUser, CropVariety $cropVariety): bool
    {
        return $authUser->can('ForceDelete:CropVariety');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CropVariety');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CropVariety');
    }

    public function replicate(AuthUser $authUser, CropVariety $cropVariety): bool
    {
        return $authUser->can('Replicate:CropVariety');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CropVariety');
    }

}