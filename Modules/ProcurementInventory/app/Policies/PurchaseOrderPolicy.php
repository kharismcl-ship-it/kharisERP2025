<?php

namespace Modules\ProcurementInventory\Policies;

use App\Models\User;
use Modules\ProcurementInventory\Models\PurchaseOrder;

class PurchaseOrderPolicy
{
    public function viewAny(User $user): bool { return $user->can('view_any_purchase_order'); }
    public function view(User $user, PurchaseOrder $model): bool { return $user->can('view_purchase_order'); }
    public function create(User $user): bool { return $user->can('create_purchase_order'); }
    public function update(User $user, PurchaseOrder $model): bool { return $user->can('update_purchase_order'); }
    public function delete(User $user, PurchaseOrder $model): bool { return $user->can('delete_purchase_order'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_purchase_order'); }
}