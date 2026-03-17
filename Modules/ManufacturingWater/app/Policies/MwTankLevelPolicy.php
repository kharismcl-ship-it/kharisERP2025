<?php

declare(strict_types=1);

namespace Modules\ManufacturingWater\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ManufacturingWater\Models\MwTankLevel;
use Illuminate\Auth\Access\HandlesAuthorization;

class MwTankLevelPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MwTankLevel');
    }

    public function view(AuthUser $authUser, MwTankLevel $mwTankLevel): bool
    {
        return $authUser->can('View:MwTankLevel');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MwTankLevel');
    }

    public function update(AuthUser $authUser, MwTankLevel $mwTankLevel): bool
    {
        return $authUser->can('Update:MwTankLevel');
    }

    public function delete(AuthUser $authUser, MwTankLevel $mwTankLevel): bool
    {
        return $authUser->can('Delete:MwTankLevel');
    }

    public function restore(AuthUser $authUser, MwTankLevel $mwTankLevel): bool
    {
        return $authUser->can('Restore:MwTankLevel');
    }

    public function forceDelete(AuthUser $authUser, MwTankLevel $mwTankLevel): bool
    {
        return $authUser->can('ForceDelete:MwTankLevel');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MwTankLevel');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MwTankLevel');
    }

    public function replicate(AuthUser $authUser, MwTankLevel $mwTankLevel): bool
    {
        return $authUser->can('Replicate:MwTankLevel');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MwTankLevel');
    }

}