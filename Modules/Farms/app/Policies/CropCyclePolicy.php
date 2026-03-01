<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\CropCycle;

class CropCyclePolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_crop_cycle'); }
    public function view(User $user, CropCycle $r): bool { return $user->can('view_crop_cycle'); }
    public function create(User $user): bool   { return $user->can('create_crop_cycle'); }
    public function update(User $user, CropCycle $r): bool { return $user->can('update_crop_cycle'); }
    public function delete(User $user, CropCycle $r): bool { return $user->can('delete_crop_cycle'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_crop_cycle'); }
}
