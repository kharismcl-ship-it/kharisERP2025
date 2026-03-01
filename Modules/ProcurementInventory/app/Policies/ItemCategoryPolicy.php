<?php

namespace Modules\ProcurementInventory\Policies;

use App\Models\User;
use Modules\ProcurementInventory\Models\ItemCategory;

class ItemCategoryPolicy
{
    public function viewAny(User $user): bool { return $user->can('view_any_item_category'); }
    public function view(User $user, ItemCategory $model): bool { return $user->can('view_item_category'); }
    public function create(User $user): bool { return $user->can('create_item_category'); }
    public function update(User $user, ItemCategory $model): bool { return $user->can('update_item_category'); }
    public function delete(User $user, ItemCategory $model): bool { return $user->can('delete_item_category'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_item_category'); }
}
