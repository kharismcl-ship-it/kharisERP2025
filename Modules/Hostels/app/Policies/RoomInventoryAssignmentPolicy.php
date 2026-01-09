<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\RoomInventoryAssignment;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoomInventoryAssignmentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RoomInventoryAssignment');
    }

    public function view(AuthUser $authUser, RoomInventoryAssignment $roomInventoryAssignment): bool
    {
        return $authUser->can('View:RoomInventoryAssignment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RoomInventoryAssignment');
    }

    public function update(AuthUser $authUser, RoomInventoryAssignment $roomInventoryAssignment): bool
    {
        return $authUser->can('Update:RoomInventoryAssignment');
    }

    public function delete(AuthUser $authUser, RoomInventoryAssignment $roomInventoryAssignment): bool
    {
        return $authUser->can('Delete:RoomInventoryAssignment');
    }

    public function restore(AuthUser $authUser, RoomInventoryAssignment $roomInventoryAssignment): bool
    {
        return $authUser->can('Restore:RoomInventoryAssignment');
    }

    public function forceDelete(AuthUser $authUser, RoomInventoryAssignment $roomInventoryAssignment): bool
    {
        return $authUser->can('ForceDelete:RoomInventoryAssignment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RoomInventoryAssignment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RoomInventoryAssignment');
    }

    public function replicate(AuthUser $authUser, RoomInventoryAssignment $roomInventoryAssignment): bool
    {
        return $authUser->can('Replicate:RoomInventoryAssignment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RoomInventoryAssignment');
    }

}