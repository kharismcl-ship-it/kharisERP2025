<?php

namespace Modules\ProcurementInventory\Policies;

use App\Models\User;
use Modules\ProcurementInventory\Models\Item;

class ItemPolicy
{
    public function viewAny(User $user): bool { return $user->can('view_any_item'); }
    public function view(User $user, Item $model): bool { return $user->can('view_item'); }
    public function create(User $user): bool { return $user->can('create_item'); }
    public function update(User $user, Item $model): bool { return $user->can('update_item'); }
    public function delete(User $user, Item $model): bool { return $user->can('delete_item'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_item'); }
}