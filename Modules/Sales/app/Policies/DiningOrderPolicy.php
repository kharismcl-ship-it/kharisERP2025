<?php

namespace Modules\Sales\Policies;

use App\Models\User;
use Modules\Sales\Models\DiningOrder;

class DiningOrderPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_dining_order'); }
    public function view(User $user, DiningOrder $model): bool  { return $user->can('view_dining_order'); }
    public function create(User $user): bool   { return $user->can('create_dining_order'); }
    public function update(User $user, DiningOrder $model): bool { return $user->can('update_dining_order'); }
    public function delete(User $user, DiningOrder $model): bool { return $user->can('delete_dining_order'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_dining_order'); }
}