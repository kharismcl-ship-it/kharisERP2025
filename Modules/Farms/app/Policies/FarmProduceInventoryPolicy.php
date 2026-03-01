<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\FarmProduceInventory;

class FarmProduceInventoryPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_farm_produce_inventory'); }
    public function view(User $user, FarmProduceInventory $record): bool { return $user->can('view_farm_produce_inventory'); }
    public function create(User $user): bool   { return $user->can('create_farm_produce_inventory'); }
    public function update(User $user, FarmProduceInventory $record): bool { return $user->can('update_farm_produce_inventory'); }
    public function delete(User $user, FarmProduceInventory $record): bool { return $user->can('delete_farm_produce_inventory'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_farm_produce_inventory'); }
}