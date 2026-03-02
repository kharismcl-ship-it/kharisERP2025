<?php

declare(strict_types=1);

namespace Modules\ManufacturingWater\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ManufacturingWater\Models\MwWaterTestRecord;
use Illuminate\Auth\Access\HandlesAuthorization;

class MwWaterTestRecordPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MwWaterTestRecord');
    }

    public function view(AuthUser $authUser, MwWaterTestRecord $mwWaterTestRecord): bool
    {
        return $authUser->can('View:MwWaterTestRecord');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MwWaterTestRecord');
    }

    public function update(AuthUser $authUser, MwWaterTestRecord $mwWaterTestRecord): bool
    {
        return $authUser->can('Update:MwWaterTestRecord');
    }

    public function delete(AuthUser $authUser, MwWaterTestRecord $mwWaterTestRecord): bool
    {
        return $authUser->can('Delete:MwWaterTestRecord');
    }

    public function restore(AuthUser $authUser, MwWaterTestRecord $mwWaterTestRecord): bool
    {
        return $authUser->can('Restore:MwWaterTestRecord');
    }

    public function forceDelete(AuthUser $authUser, MwWaterTestRecord $mwWaterTestRecord): bool
    {
        return $authUser->can('ForceDelete:MwWaterTestRecord');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MwWaterTestRecord');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MwWaterTestRecord');
    }

    public function replicate(AuthUser $authUser, MwWaterTestRecord $mwWaterTestRecord): bool
    {
        return $authUser->can('Replicate:MwWaterTestRecord');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MwWaterTestRecord');
    }

}