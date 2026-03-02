<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\HostelInventoryItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostelInventoryItemPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HostelInventoryItem');
    }

    public function view(AuthUser $authUser, HostelInventoryItem $hostelInventoryItem): bool
    {
        return $authUser->can('View:HostelInventoryItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HostelInventoryItem');
    }

    public function update(AuthUser $authUser, HostelInventoryItem $hostelInventoryItem): bool
    {
        return $authUser->can('Update:HostelInventoryItem');
    }

    public function delete(AuthUser $authUser, HostelInventoryItem $hostelInventoryItem): bool
    {
        return $authUser->can('Delete:HostelInventoryItem');
    }

    public function restore(AuthUser $authUser, HostelInventoryItem $hostelInventoryItem): bool
    {
        return $authUser->can('Restore:HostelInventoryItem');
    }

    public function forceDelete(AuthUser $authUser, HostelInventoryItem $hostelInventoryItem): bool
    {
        return $authUser->can('ForceDelete:HostelInventoryItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HostelInventoryItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HostelInventoryItem');
    }

    public function replicate(AuthUser $authUser, HostelInventoryItem $hostelInventoryItem): bool
    {
        return $authUser->can('Replicate:HostelInventoryItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HostelInventoryItem');
    }

}