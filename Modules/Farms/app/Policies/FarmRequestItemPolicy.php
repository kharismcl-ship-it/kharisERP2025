<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\FarmRequestItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class FarmRequestItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FarmRequestItem');
    }

    public function view(AuthUser $authUser, FarmRequestItem $record): bool
    {
        return $authUser->can('View:FarmRequestItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FarmRequestItem');
    }

    public function update(AuthUser $authUser, FarmRequestItem $record): bool
    {
        return $authUser->can('Update:FarmRequestItem');
    }

    public function delete(AuthUser $authUser, FarmRequestItem $record): bool
    {
        return $authUser->can('Delete:FarmRequestItem');
    }

    public function restore(AuthUser $authUser, FarmRequestItem $record): bool
    {
        return $authUser->can('Restore:FarmRequestItem');
    }

    public function forceDelete(AuthUser $authUser, FarmRequestItem $record): bool
    {
        return $authUser->can('ForceDelete:FarmRequestItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FarmRequestItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FarmRequestItem');
    }

    public function replicate(AuthUser $authUser, FarmRequestItem $record): bool
    {
        return $authUser->can('Replicate:FarmRequestItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FarmRequestItem');
    }
}
