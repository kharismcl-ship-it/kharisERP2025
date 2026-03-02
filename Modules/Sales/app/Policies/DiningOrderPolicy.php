<?php

declare(strict_types=1);

namespace Modules\Sales\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Sales\Models\DiningOrder;
use Illuminate\Auth\Access\HandlesAuthorization;

class DiningOrderPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DiningOrder');
    }

    public function view(AuthUser $authUser, DiningOrder $diningOrder): bool
    {
        return $authUser->can('View:DiningOrder');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DiningOrder');
    }

    public function update(AuthUser $authUser, DiningOrder $diningOrder): bool
    {
        return $authUser->can('Update:DiningOrder');
    }

    public function delete(AuthUser $authUser, DiningOrder $diningOrder): bool
    {
        return $authUser->can('Delete:DiningOrder');
    }

    public function restore(AuthUser $authUser, DiningOrder $diningOrder): bool
    {
        return $authUser->can('Restore:DiningOrder');
    }

    public function forceDelete(AuthUser $authUser, DiningOrder $diningOrder): bool
    {
        return $authUser->can('ForceDelete:DiningOrder');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DiningOrder');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DiningOrder');
    }

    public function replicate(AuthUser $authUser, DiningOrder $diningOrder): bool
    {
        return $authUser->can('Replicate:DiningOrder');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DiningOrder');
    }

}