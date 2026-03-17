<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\HostelBookOrder;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostelBookOrderPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HostelBookOrder');
    }

    public function view(AuthUser $authUser, HostelBookOrder $hostelBookOrder): bool
    {
        return $authUser->can('View:HostelBookOrder');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HostelBookOrder');
    }

    public function update(AuthUser $authUser, HostelBookOrder $hostelBookOrder): bool
    {
        return $authUser->can('Update:HostelBookOrder');
    }

    public function delete(AuthUser $authUser, HostelBookOrder $hostelBookOrder): bool
    {
        return $authUser->can('Delete:HostelBookOrder');
    }

    public function restore(AuthUser $authUser, HostelBookOrder $hostelBookOrder): bool
    {
        return $authUser->can('Restore:HostelBookOrder');
    }

    public function forceDelete(AuthUser $authUser, HostelBookOrder $hostelBookOrder): bool
    {
        return $authUser->can('ForceDelete:HostelBookOrder');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HostelBookOrder');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HostelBookOrder');
    }

    public function replicate(AuthUser $authUser, HostelBookOrder $hostelBookOrder): bool
    {
        return $authUser->can('Replicate:HostelBookOrder');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HostelBookOrder');
    }

}