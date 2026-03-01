<?php

namespace Modules\ProcurementInventory\Policies;

use App\Models\User;
use Modules\ProcurementInventory\Models\StockLevel;

class StockLevelPolicy
{
    public function viewAny(User $user): bool { return $user->can('view_any_stock_level'); }
    public function view(User $user, StockLevel $model): bool { return $user->can('view_stock_level'); }
    public function create(User $user): bool { return $user->can('create_stock_level'); }
    public function update(User $user, StockLevel $model): bool { return $user->can('update_stock_level'); }
    public function delete(User $user, StockLevel $model): bool { return $user->can('delete_stock_level'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_stock_level'); }
}