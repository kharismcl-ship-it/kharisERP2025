<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\CropScoutingRecord;
use Illuminate\Auth\Access\HandlesAuthorization;

class CropScoutingRecordPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CropScoutingRecord');
    }

    public function view(AuthUser $authUser, CropScoutingRecord $cropScoutingRecord): bool
    {
        return $authUser->can('View:CropScoutingRecord');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CropScoutingRecord');
    }

    public function update(AuthUser $authUser, CropScoutingRecord $cropScoutingRecord): bool
    {
        return $authUser->can('Update:CropScoutingRecord');
    }

    public function delete(AuthUser $authUser, CropScoutingRecord $cropScoutingRecord): bool
    {
        return $authUser->can('Delete:CropScoutingRecord');
    }

    public function restore(AuthUser $authUser, CropScoutingRecord $cropScoutingRecord): bool
    {
        return $authUser->can('Restore:CropScoutingRecord');
    }

    public function forceDelete(AuthUser $authUser, CropScoutingRecord $cropScoutingRecord): bool
    {
        return $authUser->can('ForceDelete:CropScoutingRecord');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CropScoutingRecord');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CropScoutingRecord');
    }

    public function replicate(AuthUser $authUser, CropScoutingRecord $cropScoutingRecord): bool
    {
        return $authUser->can('Replicate:CropScoutingRecord');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CropScoutingRecord');
    }

}