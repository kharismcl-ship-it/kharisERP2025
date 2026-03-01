<?php

namespace Modules\Sales\Policies;

use App\Models\User;
use Modules\Sales\Models\DiningTable;

class DiningTablePolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_dining_table'); }
    public function view(User $user, DiningTable $model): bool  { return $user->can('view_dining_table'); }
    public function create(User $user): bool   { return $user->can('create_dining_table'); }
    public function update(User $user, DiningTable $model): bool { return $user->can('update_dining_table'); }
    public function delete(User $user, DiningTable $model): bool { return $user->can('delete_dining_table'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_dining_table'); }
}