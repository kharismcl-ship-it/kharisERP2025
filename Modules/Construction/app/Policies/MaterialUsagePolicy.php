<?php

namespace Modules\Construction\Policies;

use App\Models\User;
use Modules\Construction\Models\MaterialUsage;

class MaterialUsagePolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_material_usage'); }
    public function view(User $user, MaterialUsage $r): bool { return $user->can('view_material_usage'); }
    public function create(User $user): bool   { return $user->can('create_material_usage'); }
    public function update(User $user, MaterialUsage $r): bool { return $user->can('update_material_usage'); }
    public function delete(User $user, MaterialUsage $r): bool { return $user->can('delete_material_usage'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_material_usage'); }
}
