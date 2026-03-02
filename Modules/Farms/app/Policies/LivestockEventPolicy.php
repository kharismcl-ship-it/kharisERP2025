<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\LivestockEvent;
use Illuminate\Auth\Access\HandlesAuthorization;

class LivestockEventPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LivestockEvent');
    }

    public function view(AuthUser $authUser, LivestockEvent $livestockEvent): bool
    {
        return $authUser->can('View:LivestockEvent');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LivestockEvent');
    }

    public function update(AuthUser $authUser, LivestockEvent $livestockEvent): bool
    {
        return $authUser->can('Update:LivestockEvent');
    }

    public function delete(AuthUser $authUser, LivestockEvent $livestockEvent): bool
    {
        return $authUser->can('Delete:LivestockEvent');
    }

    public function restore(AuthUser $authUser, LivestockEvent $livestockEvent): bool
    {
        return $authUser->can('Restore:LivestockEvent');
    }

    public function forceDelete(AuthUser $authUser, LivestockEvent $livestockEvent): bool
    {
        return $authUser->can('ForceDelete:LivestockEvent');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LivestockEvent');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LivestockEvent');
    }

    public function replicate(AuthUser $authUser, LivestockEvent $livestockEvent): bool
    {
        return $authUser->can('Replicate:LivestockEvent');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LivestockEvent');
    }

}