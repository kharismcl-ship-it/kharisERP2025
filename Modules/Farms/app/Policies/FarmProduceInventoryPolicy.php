<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\FarmProduceInventory;
use Illuminate\Auth\Access\HandlesAuthorization;

class FarmProduceInventoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FarmProduceInventory');
    }

    public function view(AuthUser $authUser, FarmProduceInventory $farmProduceInventory): bool
    {
        return $authUser->can('View:FarmProduceInventory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FarmProduceInventory');
    }

    public function update(AuthUser $authUser, FarmProduceInventory $farmProduceInventory): bool
    {
        return $authUser->can('Update:FarmProduceInventory');
    }

    public function delete(AuthUser $authUser, FarmProduceInventory $farmProduceInventory): bool
    {
        return $authUser->can('Delete:FarmProduceInventory');
    }

    public function restore(AuthUser $authUser, FarmProduceInventory $farmProduceInventory): bool
    {
        return $authUser->can('Restore:FarmProduceInventory');
    }

    public function forceDelete(AuthUser $authUser, FarmProduceInventory $farmProduceInventory): bool
    {
        return $authUser->can('ForceDelete:FarmProduceInventory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FarmProduceInventory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FarmProduceInventory');
    }

    public function replicate(AuthUser $authUser, FarmProduceInventory $farmProduceInventory): bool
    {
        return $authUser->can('Replicate:FarmProduceInventory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FarmProduceInventory');
    }

}