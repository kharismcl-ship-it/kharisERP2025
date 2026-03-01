<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\FarmSale;

class FarmSalePolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_farm_sale'); }
    public function view(User $user, FarmSale $record): bool { return $user->can('view_farm_sale'); }
    public function create(User $user): bool   { return $user->can('create_farm_sale'); }
    public function update(User $user, FarmSale $record): bool { return $user->can('update_farm_sale'); }
    public function delete(User $user, FarmSale $record): bool { return $user->can('delete_farm_sale'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_farm_sale'); }
}