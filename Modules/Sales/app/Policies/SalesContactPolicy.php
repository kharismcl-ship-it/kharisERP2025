<?php

declare(strict_types=1);

namespace Modules\Sales\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Sales\Models\SalesContact;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalesContactPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SalesContact');
    }

    public function view(AuthUser $authUser, SalesContact $salesContact): bool
    {
        return $authUser->can('View:SalesContact');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SalesContact');
    }

    public function update(AuthUser $authUser, SalesContact $salesContact): bool
    {
        return $authUser->can('Update:SalesContact');
    }

    public function delete(AuthUser $authUser, SalesContact $salesContact): bool
    {
        return $authUser->can('Delete:SalesContact');
    }

    public function restore(AuthUser $authUser, SalesContact $salesContact): bool
    {
        return $authUser->can('Restore:SalesContact');
    }

    public function forceDelete(AuthUser $authUser, SalesContact $salesContact): bool
    {
        return $authUser->can('ForceDelete:SalesContact');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SalesContact');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SalesContact');
    }

    public function replicate(AuthUser $authUser, SalesContact $salesContact): bool
    {
        return $authUser->can('Replicate:SalesContact');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SalesContact');
    }

}