<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ProcurementInventory\Models\StockLot;

class StockLotPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StockLot');
    }

    public function view(AuthUser $authUser, StockLot $record): bool
    {
        return $authUser->can('View:StockLot');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StockLot');
    }

    public function update(AuthUser $authUser, StockLot $record): bool
    {
        return $authUser->can('Update:StockLot');
    }

    public function delete(AuthUser $authUser, StockLot $record): bool
    {
        return $authUser->can('Delete:StockLot');
    }

    public function restore(AuthUser $authUser, StockLot $record): bool
    {
        return $authUser->can('Restore:StockLot');
    }

    public function forceDelete(AuthUser $authUser, StockLot $record): bool
    {
        return $authUser->can('ForceDelete:StockLot');
    }
}
