<?php

declare(strict_types=1);

namespace Modules\ITSupport\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ITSupport\Models\ItActivity;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItActivityPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ItActivity');
    }

    public function view(AuthUser $authUser, ItActivity $itActivity): bool
    {
        return $authUser->can('View:ItActivity');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ItActivity');
    }

    public function update(AuthUser $authUser, ItActivity $itActivity): bool
    {
        return $authUser->can('Update:ItActivity');
    }

    public function delete(AuthUser $authUser, ItActivity $itActivity): bool
    {
        return $authUser->can('Delete:ItActivity');
    }

    public function restore(AuthUser $authUser, ItActivity $itActivity): bool
    {
        return $authUser->can('Restore:ItActivity');
    }

    public function forceDelete(AuthUser $authUser, ItActivity $itActivity): bool
    {
        return $authUser->can('ForceDelete:ItActivity');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ItActivity');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ItActivity');
    }

    public function replicate(AuthUser $authUser, ItActivity $itActivity): bool
    {
        return $authUser->can('Replicate:ItActivity');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ItActivity');
    }

}