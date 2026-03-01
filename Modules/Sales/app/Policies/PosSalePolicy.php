<?php

namespace Modules\Sales\Policies;

use App\Models\User;
use Modules\Sales\Models\PosSale;

class PosSalePolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_pos_sale'); }
    public function view(User $user, PosSale $model): bool  { return $user->can('view_pos_sale'); }
    public function create(User $user): bool   { return $user->can('create_pos_sale'); }
    public function update(User $user, PosSale $model): bool { return $user->can('update_pos_sale'); }
    public function delete(User $user, PosSale $model): bool { return $user->can('delete_pos_sale'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_pos_sale'); }
}