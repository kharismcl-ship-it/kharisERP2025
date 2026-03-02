<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\FarmWeatherLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class FarmWeatherLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FarmWeatherLog');
    }

    public function view(AuthUser $authUser, FarmWeatherLog $farmWeatherLog): bool
    {
        return $authUser->can('View:FarmWeatherLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FarmWeatherLog');
    }

    public function update(AuthUser $authUser, FarmWeatherLog $farmWeatherLog): bool
    {
        return $authUser->can('Update:FarmWeatherLog');
    }

    public function delete(AuthUser $authUser, FarmWeatherLog $farmWeatherLog): bool
    {
        return $authUser->can('Delete:FarmWeatherLog');
    }

    public function restore(AuthUser $authUser, FarmWeatherLog $farmWeatherLog): bool
    {
        return $authUser->can('Restore:FarmWeatherLog');
    }

    public function forceDelete(AuthUser $authUser, FarmWeatherLog $farmWeatherLog): bool
    {
        return $authUser->can('ForceDelete:FarmWeatherLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FarmWeatherLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FarmWeatherLog');
    }

    public function replicate(AuthUser $authUser, FarmWeatherLog $farmWeatherLog): bool
    {
        return $authUser->can('Replicate:FarmWeatherLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FarmWeatherLog');
    }

}