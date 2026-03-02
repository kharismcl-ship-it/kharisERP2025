<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\Bed;
use Illuminate\Auth\Access\HandlesAuthorization;

class BedPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Bed');
    }

    public function view(AuthUser $authUser, Bed $bed): bool
    {
        return $authUser->can('View:Bed');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Bed');
    }

    public function update(AuthUser $authUser, Bed $bed): bool
    {
        return $authUser->can('Update:Bed');
    }

    public function delete(AuthUser $authUser, Bed $bed): bool
    {
        return $authUser->can('Delete:Bed');
    }

    public function restore(AuthUser $authUser, Bed $bed): bool
    {
        return $authUser->can('Restore:Bed');
    }

    public function forceDelete(AuthUser $authUser, Bed $bed): bool
    {
        return $authUser->can('ForceDelete:Bed');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Bed');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Bed');
    }

    public function replicate(AuthUser $authUser, Bed $bed): bool
    {
        return $authUser->can('Replicate:Bed');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Bed');
    }

}