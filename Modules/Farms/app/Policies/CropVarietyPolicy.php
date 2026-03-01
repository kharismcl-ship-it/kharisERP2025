<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\CropVariety;

class CropVarietyPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_crop_variety'); }
    public function view(User $user, CropVariety $record): bool { return $user->can('view_crop_variety'); }
    public function create(User $user): bool   { return $user->can('create_crop_variety'); }
    public function update(User $user, CropVariety $record): bool { return $user->can('update_crop_variety'); }
    public function delete(User $user, CropVariety $record): bool { return $user->can('delete_crop_variety'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_crop_variety'); }
}