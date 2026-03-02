<?php

declare(strict_types=1);

namespace Modules\Sales\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Sales\Models\SalesOrganization;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalesOrganizationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SalesOrganization');
    }

    public function view(AuthUser $authUser, SalesOrganization $salesOrganization): bool
    {
        return $authUser->can('View:SalesOrganization');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SalesOrganization');
    }

    public function update(AuthUser $authUser, SalesOrganization $salesOrganization): bool
    {
        return $authUser->can('Update:SalesOrganization');
    }

    public function delete(AuthUser $authUser, SalesOrganization $salesOrganization): bool
    {
        return $authUser->can('Delete:SalesOrganization');
    }

    public function restore(AuthUser $authUser, SalesOrganization $salesOrganization): bool
    {
        return $authUser->can('Restore:SalesOrganization');
    }

    public function forceDelete(AuthUser $authUser, SalesOrganization $salesOrganization): bool
    {
        return $authUser->can('ForceDelete:SalesOrganization');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SalesOrganization');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SalesOrganization');
    }

    public function replicate(AuthUser $authUser, SalesOrganization $salesOrganization): bool
    {
        return $authUser->can('Replicate:SalesOrganization');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SalesOrganization');
    }

}