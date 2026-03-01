<?php

namespace Modules\ProcurementInventory\Policies;

use App\Models\User;
use Modules\ProcurementInventory\Models\GoodsReceipt;

class GoodsReceiptPolicy
{
    public function viewAny(User $user): bool { return $user->can('view_any_goods_receipt'); }
    public function view(User $user, GoodsReceipt $model): bool { return $user->can('view_goods_receipt'); }
    public function create(User $user): bool { return $user->can('create_goods_receipt'); }
    public function update(User $user, GoodsReceipt $model): bool { return $user->can('update_goods_receipt'); }
    public function delete(User $user, GoodsReceipt $model): bool { return $user->can('delete_goods_receipt'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_goods_receipt'); }
}