<?php

namespace Modules\Construction\Policies;

use App\Models\User;
use Modules\Construction\Models\ConstructionProject;

class ConstructionProjectPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_construction_project'); }
    public function view(User $user, ConstructionProject $r): bool { return $user->can('view_construction_project'); }
    public function create(User $user): bool   { return $user->can('create_construction_project'); }
    public function update(User $user, ConstructionProject $r): bool { return $user->can('update_construction_project'); }
    public function delete(User $user, ConstructionProject $r): bool { return $user->can('delete_construction_project'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_construction_project'); }
}
