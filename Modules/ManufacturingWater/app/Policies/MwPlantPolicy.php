<?php

namespace Modules\ManufacturingWater\Policies;

use App\Models\User;
use Modules\ManufacturingWater\Models\MwPlant;

class MwPlantPolicy
{
    public function viewAny(User $user): bool   { return $user->can('view_any_mw::plant'); }
    public function view(User $user, MwPlant $model): bool { return $user->can('view_mw::plant'); }
    public function create(User $user): bool    { return $user->can('create_mw::plant'); }
    public function update(User $user, MwPlant $model): bool { return $user->can('update_mw::plant'); }
    public function delete(User $user, MwPlant $model): bool { return $user->can('delete_mw::plant'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_mw::plant'); }
}
