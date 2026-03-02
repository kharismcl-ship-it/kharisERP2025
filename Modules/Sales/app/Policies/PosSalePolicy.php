<?php

declare(strict_types=1);

namespace Modules\Sales\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Sales\Models\PosSale;
use Illuminate\Auth\Access\HandlesAuthorization;

class PosSalePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PosSale');
    }

    public function view(AuthUser $authUser, PosSale $posSale): bool
    {
        return $authUser->can('View:PosSale');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PosSale');
    }

    public function update(AuthUser $authUser, PosSale $posSale): bool
    {
        return $authUser->can('Update:PosSale');
    }

    public function delete(AuthUser $authUser, PosSale $posSale): bool
    {
        return $authUser->can('Delete:PosSale');
    }

    public function restore(AuthUser $authUser, PosSale $posSale): bool
    {
        return $authUser->can('Restore:PosSale');
    }

    public function forceDelete(AuthUser $authUser, PosSale $posSale): bool
    {
        return $authUser->can('ForceDelete:PosSale');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PosSale');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PosSale');
    }

    public function replicate(AuthUser $authUser, PosSale $posSale): bool
    {
        return $authUser->can('Replicate:PosSale');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PosSale');
    }

}