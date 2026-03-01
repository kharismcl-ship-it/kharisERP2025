<?php

namespace Modules\ProcurementInventory\Policies;

use App\Models\User;
use Modules\ProcurementInventory\Models\Warehouse;

class WarehousePolicy
{
    public function viewAny(User $user): bool   { return $user->can('view_any_warehouse'); }
    public function view(User $user, Warehouse $model): bool { return $user->can('view_warehouse'); }
    public function create(User $user): bool    { return $user->can('create_warehouse'); }
    public function update(User $user, Warehouse $model): bool { return $user->can('update_warehouse'); }
    public function delete(User $user, Warehouse $model): bool { return $user->can('delete_warehouse'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_warehouse'); }
}