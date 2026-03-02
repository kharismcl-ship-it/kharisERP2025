<?php

declare(strict_types=1);

namespace Modules\Sales\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Sales\Models\SalesLead;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalesLeadPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SalesLead');
    }

    public function view(AuthUser $authUser, SalesLead $salesLead): bool
    {
        return $authUser->can('View:SalesLead');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SalesLead');
    }

    public function update(AuthUser $authUser, SalesLead $salesLead): bool
    {
        return $authUser->can('Update:SalesLead');
    }

    public function delete(AuthUser $authUser, SalesLead $salesLead): bool
    {
        return $authUser->can('Delete:SalesLead');
    }

    public function restore(AuthUser $authUser, SalesLead $salesLead): bool
    {
        return $authUser->can('Restore:SalesLead');
    }

    public function forceDelete(AuthUser $authUser, SalesLead $salesLead): bool
    {
        return $authUser->can('ForceDelete:SalesLead');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SalesLead');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SalesLead');
    }

    public function replicate(AuthUser $authUser, SalesLead $salesLead): bool
    {
        return $authUser->can('Replicate:SalesLead');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SalesLead');
    }

}