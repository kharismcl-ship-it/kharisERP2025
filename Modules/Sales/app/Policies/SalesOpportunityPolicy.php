<?php

declare(strict_types=1);

namespace Modules\Sales\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Sales\Models\SalesOpportunity;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalesOpportunityPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SalesOpportunity');
    }

    public function view(AuthUser $authUser, SalesOpportunity $salesOpportunity): bool
    {
        return $authUser->can('View:SalesOpportunity');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SalesOpportunity');
    }

    public function update(AuthUser $authUser, SalesOpportunity $salesOpportunity): bool
    {
        return $authUser->can('Update:SalesOpportunity');
    }

    public function delete(AuthUser $authUser, SalesOpportunity $salesOpportunity): bool
    {
        return $authUser->can('Delete:SalesOpportunity');
    }

    public function restore(AuthUser $authUser, SalesOpportunity $salesOpportunity): bool
    {
        return $authUser->can('Restore:SalesOpportunity');
    }

    public function forceDelete(AuthUser $authUser, SalesOpportunity $salesOpportunity): bool
    {
        return $authUser->can('ForceDelete:SalesOpportunity');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SalesOpportunity');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SalesOpportunity');
    }

    public function replicate(AuthUser $authUser, SalesOpportunity $salesOpportunity): bool
    {
        return $authUser->can('Replicate:SalesOpportunity');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SalesOpportunity');
    }

}