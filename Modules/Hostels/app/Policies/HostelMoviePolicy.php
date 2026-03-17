<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\HostelMovie;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostelMoviePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HostelMovie');
    }

    public function view(AuthUser $authUser, HostelMovie $hostelMovie): bool
    {
        return $authUser->can('View:HostelMovie');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HostelMovie');
    }

    public function update(AuthUser $authUser, HostelMovie $hostelMovie): bool
    {
        return $authUser->can('Update:HostelMovie');
    }

    public function delete(AuthUser $authUser, HostelMovie $hostelMovie): bool
    {
        return $authUser->can('Delete:HostelMovie');
    }

    public function restore(AuthUser $authUser, HostelMovie $hostelMovie): bool
    {
        return $authUser->can('Restore:HostelMovie');
    }

    public function forceDelete(AuthUser $authUser, HostelMovie $hostelMovie): bool
    {
        return $authUser->can('ForceDelete:HostelMovie');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HostelMovie');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HostelMovie');
    }

    public function replicate(AuthUser $authUser, HostelMovie $hostelMovie): bool
    {
        return $authUser->can('Replicate:HostelMovie');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HostelMovie');
    }

}