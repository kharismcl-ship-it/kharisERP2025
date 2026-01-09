<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\HostelBlock;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostelBlockPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HostelBlock');
    }

    public function view(AuthUser $authUser, HostelBlock $hostelBlock): bool
    {
        return $authUser->can('View:HostelBlock');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HostelBlock');
    }

    public function update(AuthUser $authUser, HostelBlock $hostelBlock): bool
    {
        return $authUser->can('Update:HostelBlock');
    }

    public function delete(AuthUser $authUser, HostelBlock $hostelBlock): bool
    {
        return $authUser->can('Delete:HostelBlock');
    }

    public function restore(AuthUser $authUser, HostelBlock $hostelBlock): bool
    {
        return $authUser->can('Restore:HostelBlock');
    }

    public function forceDelete(AuthUser $authUser, HostelBlock $hostelBlock): bool
    {
        return $authUser->can('ForceDelete:HostelBlock');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HostelBlock');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HostelBlock');
    }

    public function replicate(AuthUser $authUser, HostelBlock $hostelBlock): bool
    {
        return $authUser->can('Replicate:HostelBlock');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HostelBlock');
    }

}