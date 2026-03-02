<?php

declare(strict_types=1);

namespace Modules\Sales\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Sales\Models\SalesCatalog;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalesCatalogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SalesCatalog');
    }

    public function view(AuthUser $authUser, SalesCatalog $salesCatalog): bool
    {
        return $authUser->can('View:SalesCatalog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SalesCatalog');
    }

    public function update(AuthUser $authUser, SalesCatalog $salesCatalog): bool
    {
        return $authUser->can('Update:SalesCatalog');
    }

    public function delete(AuthUser $authUser, SalesCatalog $salesCatalog): bool
    {
        return $authUser->can('Delete:SalesCatalog');
    }

    public function restore(AuthUser $authUser, SalesCatalog $salesCatalog): bool
    {
        return $authUser->can('Restore:SalesCatalog');
    }

    public function forceDelete(AuthUser $authUser, SalesCatalog $salesCatalog): bool
    {
        return $authUser->can('ForceDelete:SalesCatalog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SalesCatalog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SalesCatalog');
    }

    public function replicate(AuthUser $authUser, SalesCatalog $salesCatalog): bool
    {
        return $authUser->can('Replicate:SalesCatalog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SalesCatalog');
    }

}