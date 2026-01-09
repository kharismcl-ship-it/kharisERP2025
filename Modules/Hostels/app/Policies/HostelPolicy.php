<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\Hostel;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostelPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Hostel');
    }

    public function view(AuthUser $authUser, Hostel $hostel): bool
    {
        return $authUser->can('View:Hostel');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Hostel');
    }

    public function update(AuthUser $authUser, Hostel $hostel): bool
    {
        return $authUser->can('Update:Hostel');
    }

    public function delete(AuthUser $authUser, Hostel $hostel): bool
    {
        return $authUser->can('Delete:Hostel');
    }

    public function restore(AuthUser $authUser, Hostel $hostel): bool
    {
        return $authUser->can('Restore:Hostel');
    }

    public function forceDelete(AuthUser $authUser, Hostel $hostel): bool
    {
        return $authUser->can('ForceDelete:Hostel');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Hostel');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Hostel');
    }

    public function replicate(AuthUser $authUser, Hostel $hostel): bool
    {
        return $authUser->can('Replicate:Hostel');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Hostel');
    }

}