<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\HostelOccupant;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostelOccupantPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HostelOccupant');
    }

    public function view(AuthUser $authUser, HostelOccupant $hostelOccupant): bool
    {
        return $authUser->can('View:HostelOccupant');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HostelOccupant');
    }

    public function update(AuthUser $authUser, HostelOccupant $hostelOccupant): bool
    {
        return $authUser->can('Update:HostelOccupant');
    }

    public function delete(AuthUser $authUser, HostelOccupant $hostelOccupant): bool
    {
        return $authUser->can('Delete:HostelOccupant');
    }

    public function restore(AuthUser $authUser, HostelOccupant $hostelOccupant): bool
    {
        return $authUser->can('Restore:HostelOccupant');
    }

    public function forceDelete(AuthUser $authUser, HostelOccupant $hostelOccupant): bool
    {
        return $authUser->can('ForceDelete:HostelOccupant');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HostelOccupant');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HostelOccupant');
    }

    public function replicate(AuthUser $authUser, HostelOccupant $hostelOccupant): bool
    {
        return $authUser->can('Replicate:HostelOccupant');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HostelOccupant');
    }

}