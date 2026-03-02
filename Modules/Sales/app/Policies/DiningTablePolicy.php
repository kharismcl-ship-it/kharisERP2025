<?php

declare(strict_types=1);

namespace Modules\Sales\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Sales\Models\DiningTable;
use Illuminate\Auth\Access\HandlesAuthorization;

class DiningTablePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DiningTable');
    }

    public function view(AuthUser $authUser, DiningTable $diningTable): bool
    {
        return $authUser->can('View:DiningTable');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DiningTable');
    }

    public function update(AuthUser $authUser, DiningTable $diningTable): bool
    {
        return $authUser->can('Update:DiningTable');
    }

    public function delete(AuthUser $authUser, DiningTable $diningTable): bool
    {
        return $authUser->can('Delete:DiningTable');
    }

    public function restore(AuthUser $authUser, DiningTable $diningTable): bool
    {
        return $authUser->can('Restore:DiningTable');
    }

    public function forceDelete(AuthUser $authUser, DiningTable $diningTable): bool
    {
        return $authUser->can('ForceDelete:DiningTable');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DiningTable');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DiningTable');
    }

    public function replicate(AuthUser $authUser, DiningTable $diningTable): bool
    {
        return $authUser->can('Replicate:DiningTable');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DiningTable');
    }

}