<?php

declare(strict_types=1);

namespace Modules\ManufacturingWater\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ManufacturingWater\Models\MwPlant;
use Illuminate\Auth\Access\HandlesAuthorization;

class MwPlantPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MwPlant');
    }

    public function view(AuthUser $authUser, MwPlant $mwPlant): bool
    {
        return $authUser->can('View:MwPlant');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MwPlant');
    }

    public function update(AuthUser $authUser, MwPlant $mwPlant): bool
    {
        return $authUser->can('Update:MwPlant');
    }

    public function delete(AuthUser $authUser, MwPlant $mwPlant): bool
    {
        return $authUser->can('Delete:MwPlant');
    }

    public function restore(AuthUser $authUser, MwPlant $mwPlant): bool
    {
        return $authUser->can('Restore:MwPlant');
    }

    public function forceDelete(AuthUser $authUser, MwPlant $mwPlant): bool
    {
        return $authUser->can('ForceDelete:MwPlant');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MwPlant');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MwPlant');
    }

    public function replicate(AuthUser $authUser, MwPlant $mwPlant): bool
    {
        return $authUser->can('Replicate:MwPlant');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MwPlant');
    }

}