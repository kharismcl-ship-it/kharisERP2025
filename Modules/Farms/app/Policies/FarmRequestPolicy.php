<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\FarmRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class FarmRequestPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FarmRequest');
    }

    public function view(AuthUser $authUser, FarmRequest $farmRequest): bool
    {
        return $authUser->can('View:FarmRequest');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FarmRequest');
    }

    public function update(AuthUser $authUser, FarmRequest $farmRequest): bool
    {
        return $authUser->can('Update:FarmRequest');
    }

    public function delete(AuthUser $authUser, FarmRequest $farmRequest): bool
    {
        return $authUser->can('Delete:FarmRequest');
    }

    public function restore(AuthUser $authUser, FarmRequest $farmRequest): bool
    {
        return $authUser->can('Restore:FarmRequest');
    }

    public function forceDelete(AuthUser $authUser, FarmRequest $farmRequest): bool
    {
        return $authUser->can('ForceDelete:FarmRequest');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FarmRequest');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FarmRequest');
    }

    public function replicate(AuthUser $authUser, FarmRequest $farmRequest): bool
    {
        return $authUser->can('Replicate:FarmRequest');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FarmRequest');
    }

}