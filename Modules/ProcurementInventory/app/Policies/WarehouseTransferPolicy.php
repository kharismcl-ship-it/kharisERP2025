<?php

namespace Modules\ProcurementInventory\Policies;

use App\Models\User;
use Modules\ProcurementInventory\Models\WarehouseTransfer;

class WarehouseTransferPolicy
{
    public function viewAny(User $user): bool   { return $user->can('view_any_warehouse_transfer'); }
    public function view(User $user, WarehouseTransfer $model): bool { return $user->can('view_warehouse_transfer'); }
    public function create(User $user): bool    { return $user->can('create_warehouse_transfer'); }
    public function update(User $user, WarehouseTransfer $model): bool { return $user->can('update_warehouse_transfer'); }
    public function delete(User $user, WarehouseTransfer $model): bool { return $user->can('delete_warehouse_transfer'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_warehouse_transfer'); }
}