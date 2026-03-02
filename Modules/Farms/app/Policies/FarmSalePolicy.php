<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\FarmSale;
use Illuminate\Auth\Access\HandlesAuthorization;

class FarmSalePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FarmSale');
    }

    public function view(AuthUser $authUser, FarmSale $farmSale): bool
    {
        return $authUser->can('View:FarmSale');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FarmSale');
    }

    public function update(AuthUser $authUser, FarmSale $farmSale): bool
    {
        return $authUser->can('Update:FarmSale');
    }

    public function delete(AuthUser $authUser, FarmSale $farmSale): bool
    {
        return $authUser->can('Delete:FarmSale');
    }

    public function restore(AuthUser $authUser, FarmSale $farmSale): bool
    {
        return $authUser->can('Restore:FarmSale');
    }

    public function forceDelete(AuthUser $authUser, FarmSale $farmSale): bool
    {
        return $authUser->can('ForceDelete:FarmSale');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FarmSale');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FarmSale');
    }

    public function replicate(AuthUser $authUser, FarmSale $farmSale): bool
    {
        return $authUser->can('Replicate:FarmSale');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FarmSale');
    }

}