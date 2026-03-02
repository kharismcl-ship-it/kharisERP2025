<?php

declare(strict_types=1);

namespace Modules\Sales\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Sales\Models\SalesPriceList;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalesPriceListPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SalesPriceList');
    }

    public function view(AuthUser $authUser, SalesPriceList $salesPriceList): bool
    {
        return $authUser->can('View:SalesPriceList');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SalesPriceList');
    }

    public function update(AuthUser $authUser, SalesPriceList $salesPriceList): bool
    {
        return $authUser->can('Update:SalesPriceList');
    }

    public function delete(AuthUser $authUser, SalesPriceList $salesPriceList): bool
    {
        return $authUser->can('Delete:SalesPriceList');
    }

    public function restore(AuthUser $authUser, SalesPriceList $salesPriceList): bool
    {
        return $authUser->can('Restore:SalesPriceList');
    }

    public function forceDelete(AuthUser $authUser, SalesPriceList $salesPriceList): bool
    {
        return $authUser->can('ForceDelete:SalesPriceList');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SalesPriceList');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SalesPriceList');
    }

    public function replicate(AuthUser $authUser, SalesPriceList $salesPriceList): bool
    {
        return $authUser->can('Replicate:SalesPriceList');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SalesPriceList');
    }

}