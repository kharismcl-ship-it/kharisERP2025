<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\CropInputApplication;

class CropInputApplicationPolicy
{
    public function viewAny(User $user): bool   { return $user->can('view_any_crop_input_application'); }
    public function view(User $user, CropInputApplication $r): bool { return $user->can('view_crop_input_application'); }
    public function create(User $user): bool    { return $user->can('create_crop_input_application'); }
    public function update(User $user, CropInputApplication $r): bool { return $user->can('update_crop_input_application'); }
    public function delete(User $user, CropInputApplication $r): bool { return $user->can('delete_crop_input_application'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_crop_input_application'); }
}