<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\HostelFloor;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostelFloorPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HostelFloor');
    }

    public function view(AuthUser $authUser, HostelFloor $hostelFloor): bool
    {
        return $authUser->can('View:HostelFloor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HostelFloor');
    }

    public function update(AuthUser $authUser, HostelFloor $hostelFloor): bool
    {
        return $authUser->can('Update:HostelFloor');
    }

    public function delete(AuthUser $authUser, HostelFloor $hostelFloor): bool
    {
        return $authUser->can('Delete:HostelFloor');
    }

    public function restore(AuthUser $authUser, HostelFloor $hostelFloor): bool
    {
        return $authUser->can('Restore:HostelFloor');
    }

    public function forceDelete(AuthUser $authUser, HostelFloor $hostelFloor): bool
    {
        return $authUser->can('ForceDelete:HostelFloor');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HostelFloor');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HostelFloor');
    }

    public function replicate(AuthUser $authUser, HostelFloor $hostelFloor): bool
    {
        return $authUser->can('Replicate:HostelFloor');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HostelFloor');
    }

}