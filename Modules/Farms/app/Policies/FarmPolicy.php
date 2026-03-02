<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\Farm;
use Illuminate\Auth\Access\HandlesAuthorization;

class FarmPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Farm');
    }

    public function view(AuthUser $authUser, Farm $farm): bool
    {
        return $authUser->can('View:Farm');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Farm');
    }

    public function update(AuthUser $authUser, Farm $farm): bool
    {
        return $authUser->can('Update:Farm');
    }

    public function delete(AuthUser $authUser, Farm $farm): bool
    {
        return $authUser->can('Delete:Farm');
    }

    public function restore(AuthUser $authUser, Farm $farm): bool
    {
        return $authUser->can('Restore:Farm');
    }

    public function forceDelete(AuthUser $authUser, Farm $farm): bool
    {
        return $authUser->can('ForceDelete:Farm');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Farm');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Farm');
    }

    public function replicate(AuthUser $authUser, Farm $farm): bool
    {
        return $authUser->can('Replicate:Farm');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Farm');
    }

}