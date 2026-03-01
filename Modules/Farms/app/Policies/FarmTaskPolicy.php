<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\FarmTask;

class FarmTaskPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_farm_task'); }
    public function view(User $user, FarmTask $record): bool { return $user->can('view_farm_task'); }
    public function create(User $user): bool   { return $user->can('create_farm_task'); }
    public function update(User $user, FarmTask $record): bool { return $user->can('update_farm_task'); }
    public function delete(User $user, FarmTask $record): bool { return $user->can('delete_farm_task'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_farm_task'); }
}