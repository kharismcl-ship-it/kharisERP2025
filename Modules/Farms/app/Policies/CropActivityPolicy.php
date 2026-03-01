<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\CropActivity;

class CropActivityPolicy
{
    public function viewAny(User $user): bool   { return $user->can('view_any_crop_activity'); }
    public function view(User $user, CropActivity $r): bool { return $user->can('view_crop_activity'); }
    public function create(User $user): bool    { return $user->can('create_crop_activity'); }
    public function update(User $user, CropActivity $r): bool { return $user->can('update_crop_activity'); }
    public function delete(User $user, CropActivity $r): bool { return $user->can('delete_crop_activity'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_crop_activity'); }
}