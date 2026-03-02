<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\SoilTestRecord;
use Illuminate\Auth\Access\HandlesAuthorization;

class SoilTestRecordPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SoilTestRecord');
    }

    public function view(AuthUser $authUser, SoilTestRecord $soilTestRecord): bool
    {
        return $authUser->can('View:SoilTestRecord');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SoilTestRecord');
    }

    public function update(AuthUser $authUser, SoilTestRecord $soilTestRecord): bool
    {
        return $authUser->can('Update:SoilTestRecord');
    }

    public function delete(AuthUser $authUser, SoilTestRecord $soilTestRecord): bool
    {
        return $authUser->can('Delete:SoilTestRecord');
    }

    public function restore(AuthUser $authUser, SoilTestRecord $soilTestRecord): bool
    {
        return $authUser->can('Restore:SoilTestRecord');
    }

    public function forceDelete(AuthUser $authUser, SoilTestRecord $soilTestRecord): bool
    {
        return $authUser->can('ForceDelete:SoilTestRecord');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SoilTestRecord');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SoilTestRecord');
    }

    public function replicate(AuthUser $authUser, SoilTestRecord $soilTestRecord): bool
    {
        return $authUser->can('Replicate:SoilTestRecord');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SoilTestRecord');
    }

}