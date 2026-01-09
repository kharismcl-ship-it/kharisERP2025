<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\HostelCharge;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostelChargePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HostelCharge');
    }

    public function view(AuthUser $authUser, HostelCharge $hostelCharge): bool
    {
        return $authUser->can('View:HostelCharge');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HostelCharge');
    }

    public function update(AuthUser $authUser, HostelCharge $hostelCharge): bool
    {
        return $authUser->can('Update:HostelCharge');
    }

    public function delete(AuthUser $authUser, HostelCharge $hostelCharge): bool
    {
        return $authUser->can('Delete:HostelCharge');
    }

    public function restore(AuthUser $authUser, HostelCharge $hostelCharge): bool
    {
        return $authUser->can('Restore:HostelCharge');
    }

    public function forceDelete(AuthUser $authUser, HostelCharge $hostelCharge): bool
    {
        return $authUser->can('ForceDelete:HostelCharge');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HostelCharge');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HostelCharge');
    }

    public function replicate(AuthUser $authUser, HostelCharge $hostelCharge): bool
    {
        return $authUser->can('Replicate:HostelCharge');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HostelCharge');
    }

}