<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\FarmSeason;
use Illuminate\Auth\Access\HandlesAuthorization;

class FarmSeasonPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FarmSeason');
    }

    public function view(AuthUser $authUser, FarmSeason $farmSeason): bool
    {
        return $authUser->can('View:FarmSeason');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FarmSeason');
    }

    public function update(AuthUser $authUser, FarmSeason $farmSeason): bool
    {
        return $authUser->can('Update:FarmSeason');
    }

    public function delete(AuthUser $authUser, FarmSeason $farmSeason): bool
    {
        return $authUser->can('Delete:FarmSeason');
    }

    public function restore(AuthUser $authUser, FarmSeason $farmSeason): bool
    {
        return $authUser->can('Restore:FarmSeason');
    }

    public function forceDelete(AuthUser $authUser, FarmSeason $farmSeason): bool
    {
        return $authUser->can('ForceDelete:FarmSeason');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FarmSeason');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FarmSeason');
    }

    public function replicate(AuthUser $authUser, FarmSeason $farmSeason): bool
    {
        return $authUser->can('Replicate:FarmSeason');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FarmSeason');
    }

}