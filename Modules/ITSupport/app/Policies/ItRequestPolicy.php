<?php

declare(strict_types=1);

namespace Modules\ITSupport\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ITSupport\Models\ItRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItRequestPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ItRequest');
    }

    public function view(AuthUser $authUser, ItRequest $itRequest): bool
    {
        return $authUser->can('View:ItRequest');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ItRequest');
    }

    public function update(AuthUser $authUser, ItRequest $itRequest): bool
    {
        return $authUser->can('Update:ItRequest');
    }

    public function delete(AuthUser $authUser, ItRequest $itRequest): bool
    {
        return $authUser->can('Delete:ItRequest');
    }

    public function restore(AuthUser $authUser, ItRequest $itRequest): bool
    {
        return $authUser->can('Restore:ItRequest');
    }

    public function forceDelete(AuthUser $authUser, ItRequest $itRequest): bool
    {
        return $authUser->can('ForceDelete:ItRequest');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ItRequest');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ItRequest');
    }

    public function replicate(AuthUser $authUser, ItRequest $itRequest): bool
    {
        return $authUser->can('Replicate:ItRequest');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ItRequest');
    }

}