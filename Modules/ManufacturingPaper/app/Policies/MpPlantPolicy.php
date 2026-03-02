<?php

declare(strict_types=1);

namespace Modules\ManufacturingPaper\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ManufacturingPaper\Models\MpPlant;
use Illuminate\Auth\Access\HandlesAuthorization;

class MpPlantPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MpPlant');
    }

    public function view(AuthUser $authUser, MpPlant $mpPlant): bool
    {
        return $authUser->can('View:MpPlant');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MpPlant');
    }

    public function update(AuthUser $authUser, MpPlant $mpPlant): bool
    {
        return $authUser->can('Update:MpPlant');
    }

    public function delete(AuthUser $authUser, MpPlant $mpPlant): bool
    {
        return $authUser->can('Delete:MpPlant');
    }

    public function restore(AuthUser $authUser, MpPlant $mpPlant): bool
    {
        return $authUser->can('Restore:MpPlant');
    }

    public function forceDelete(AuthUser $authUser, MpPlant $mpPlant): bool
    {
        return $authUser->can('ForceDelete:MpPlant');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MpPlant');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MpPlant');
    }

    public function replicate(AuthUser $authUser, MpPlant $mpPlant): bool
    {
        return $authUser->can('Replicate:MpPlant');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MpPlant');
    }

}