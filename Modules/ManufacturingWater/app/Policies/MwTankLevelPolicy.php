<?php

namespace Modules\ManufacturingWater\Policies;

use App\Models\User;
use Modules\ManufacturingWater\Models\MwTankLevel;

class MwTankLevelPolicy
{
    public function viewAny(User $user): bool   { return $user->can('view_any_mw::tank::level'); }
    public function view(User $user, MwTankLevel $model): bool { return $user->can('view_mw::tank::level'); }
    public function create(User $user): bool    { return $user->can('create_mw::tank::level'); }
    public function update(User $user, MwTankLevel $model): bool { return $user->can('update_mw::tank::level'); }
    public function delete(User $user, MwTankLevel $model): bool { return $user->can('delete_mw::tank::level'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_mw::tank::level'); }
}