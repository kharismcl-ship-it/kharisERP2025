<?php

declare(strict_types=1);

namespace Modules\ManufacturingPaper\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ManufacturingPaper\Models\MpEquipmentLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class MpEquipmentLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MpEquipmentLog');
    }

    public function view(AuthUser $authUser, MpEquipmentLog $mpEquipmentLog): bool
    {
        return $authUser->can('View:MpEquipmentLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MpEquipmentLog');
    }

    public function update(AuthUser $authUser, MpEquipmentLog $mpEquipmentLog): bool
    {
        return $authUser->can('Update:MpEquipmentLog');
    }

    public function delete(AuthUser $authUser, MpEquipmentLog $mpEquipmentLog): bool
    {
        return $authUser->can('Delete:MpEquipmentLog');
    }

    public function restore(AuthUser $authUser, MpEquipmentLog $mpEquipmentLog): bool
    {
        return $authUser->can('Restore:MpEquipmentLog');
    }

    public function forceDelete(AuthUser $authUser, MpEquipmentLog $mpEquipmentLog): bool
    {
        return $authUser->can('ForceDelete:MpEquipmentLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MpEquipmentLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MpEquipmentLog');
    }

    public function replicate(AuthUser $authUser, MpEquipmentLog $mpEquipmentLog): bool
    {
        return $authUser->can('Replicate:MpEquipmentLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MpEquipmentLog');
    }

}