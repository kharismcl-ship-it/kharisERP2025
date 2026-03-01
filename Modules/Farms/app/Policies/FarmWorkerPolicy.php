<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\FarmWorker;

class FarmWorkerPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_farm_worker'); }
    public function view(User $user, FarmWorker $record): bool { return $user->can('view_farm_worker'); }
    public function create(User $user): bool   { return $user->can('create_farm_worker'); }
    public function update(User $user, FarmWorker $record): bool { return $user->can('update_farm_worker'); }
    public function delete(User $user, FarmWorker $record): bool { return $user->can('delete_farm_worker'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_farm_worker'); }
}