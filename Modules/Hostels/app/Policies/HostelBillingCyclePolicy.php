<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\HostelBillingCycle;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostelBillingCyclePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HostelBillingCycle');
    }

    public function view(AuthUser $authUser, HostelBillingCycle $hostelBillingCycle): bool
    {
        return $authUser->can('View:HostelBillingCycle');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HostelBillingCycle');
    }

    public function update(AuthUser $authUser, HostelBillingCycle $hostelBillingCycle): bool
    {
        return $authUser->can('Update:HostelBillingCycle');
    }

    public function delete(AuthUser $authUser, HostelBillingCycle $hostelBillingCycle): bool
    {
        return $authUser->can('Delete:HostelBillingCycle');
    }

    public function restore(AuthUser $authUser, HostelBillingCycle $hostelBillingCycle): bool
    {
        return $authUser->can('Restore:HostelBillingCycle');
    }

    public function forceDelete(AuthUser $authUser, HostelBillingCycle $hostelBillingCycle): bool
    {
        return $authUser->can('ForceDelete:HostelBillingCycle');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HostelBillingCycle');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HostelBillingCycle');
    }

    public function replicate(AuthUser $authUser, HostelBillingCycle $hostelBillingCycle): bool
    {
        return $authUser->can('Replicate:HostelBillingCycle');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HostelBillingCycle');
    }

}