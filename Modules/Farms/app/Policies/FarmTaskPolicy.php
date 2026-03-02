<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\FarmTask;
use Illuminate\Auth\Access\HandlesAuthorization;

class FarmTaskPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FarmTask');
    }

    public function view(AuthUser $authUser, FarmTask $farmTask): bool
    {
        return $authUser->can('View:FarmTask');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FarmTask');
    }

    public function update(AuthUser $authUser, FarmTask $farmTask): bool
    {
        return $authUser->can('Update:FarmTask');
    }

    public function delete(AuthUser $authUser, FarmTask $farmTask): bool
    {
        return $authUser->can('Delete:FarmTask');
    }

    public function restore(AuthUser $authUser, FarmTask $farmTask): bool
    {
        return $authUser->can('Restore:FarmTask');
    }

    public function forceDelete(AuthUser $authUser, FarmTask $farmTask): bool
    {
        return $authUser->can('ForceDelete:FarmTask');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FarmTask');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FarmTask');
    }

    public function replicate(AuthUser $authUser, FarmTask $farmTask): bool
    {
        return $authUser->can('Replicate:FarmTask');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FarmTask');
    }

}