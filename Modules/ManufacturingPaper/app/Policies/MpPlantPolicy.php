<?php

namespace Modules\ManufacturingPaper\Policies;

use App\Models\User;
use Modules\ManufacturingPaper\Models\MpPlant;

class MpPlantPolicy
{
    public function viewAny(User $user): bool   { return $user->can('view_any_mp::plant'); }
    public function view(User $user, MpPlant $model): bool { return $user->can('view_mp::plant'); }
    public function create(User $user): bool    { return $user->can('create_mp::plant'); }
    public function update(User $user, MpPlant $model): bool { return $user->can('update_mp::plant'); }
    public function delete(User $user, MpPlant $model): bool { return $user->can('delete_mp::plant'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_mp::plant'); }
}
