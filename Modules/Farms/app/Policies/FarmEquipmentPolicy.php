<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\FarmEquipment;
use Illuminate\Auth\Access\HandlesAuthorization;

class FarmEquipmentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FarmEquipment');
    }

    public function view(AuthUser $authUser, FarmEquipment $farmEquipment): bool
    {
        return $authUser->can('View:FarmEquipment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FarmEquipment');
    }

    public function update(AuthUser $authUser, FarmEquipment $farmEquipment): bool
    {
        return $authUser->can('Update:FarmEquipment');
    }

    public function delete(AuthUser $authUser, FarmEquipment $farmEquipment): bool
    {
        return $authUser->can('Delete:FarmEquipment');
    }

    public function restore(AuthUser $authUser, FarmEquipment $farmEquipment): bool
    {
        return $authUser->can('Restore:FarmEquipment');
    }

    public function forceDelete(AuthUser $authUser, FarmEquipment $farmEquipment): bool
    {
        return $authUser->can('ForceDelete:FarmEquipment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FarmEquipment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FarmEquipment');
    }

    public function replicate(AuthUser $authUser, FarmEquipment $farmEquipment): bool
    {
        return $authUser->can('Replicate:FarmEquipment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FarmEquipment');
    }

}