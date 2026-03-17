<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\HostelBook;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostelBookPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HostelBook');
    }

    public function view(AuthUser $authUser, HostelBook $hostelBook): bool
    {
        return $authUser->can('View:HostelBook');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HostelBook');
    }

    public function update(AuthUser $authUser, HostelBook $hostelBook): bool
    {
        return $authUser->can('Update:HostelBook');
    }

    public function delete(AuthUser $authUser, HostelBook $hostelBook): bool
    {
        return $authUser->can('Delete:HostelBook');
    }

    public function restore(AuthUser $authUser, HostelBook $hostelBook): bool
    {
        return $authUser->can('Restore:HostelBook');
    }

    public function forceDelete(AuthUser $authUser, HostelBook $hostelBook): bool
    {
        return $authUser->can('ForceDelete:HostelBook');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HostelBook');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HostelBook');
    }

    public function replicate(AuthUser $authUser, HostelBook $hostelBook): bool
    {
        return $authUser->can('Replicate:HostelBook');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HostelBook');
    }

}