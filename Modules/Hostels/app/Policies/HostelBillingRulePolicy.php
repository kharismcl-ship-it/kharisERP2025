<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\HostelBillingRule;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostelBillingRulePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HostelBillingRule');
    }

    public function view(AuthUser $authUser, HostelBillingRule $hostelBillingRule): bool
    {
        return $authUser->can('View:HostelBillingRule');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HostelBillingRule');
    }

    public function update(AuthUser $authUser, HostelBillingRule $hostelBillingRule): bool
    {
        return $authUser->can('Update:HostelBillingRule');
    }

    public function delete(AuthUser $authUser, HostelBillingRule $hostelBillingRule): bool
    {
        return $authUser->can('Delete:HostelBillingRule');
    }

    public function restore(AuthUser $authUser, HostelBillingRule $hostelBillingRule): bool
    {
        return $authUser->can('Restore:HostelBillingRule');
    }

    public function forceDelete(AuthUser $authUser, HostelBillingRule $hostelBillingRule): bool
    {
        return $authUser->can('ForceDelete:HostelBillingRule');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HostelBillingRule');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HostelBillingRule');
    }

    public function replicate(AuthUser $authUser, HostelBillingRule $hostelBillingRule): bool
    {
        return $authUser->can('Replicate:HostelBillingRule');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HostelBillingRule');
    }

}