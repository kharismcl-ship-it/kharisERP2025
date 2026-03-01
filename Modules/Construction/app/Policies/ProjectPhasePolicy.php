<?php

namespace Modules\Construction\Policies;

use App\Models\User;
use Modules\Construction\Models\ProjectPhase;

class ProjectPhasePolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_project_phase'); }
    public function view(User $user, ProjectPhase $r): bool { return $user->can('view_project_phase'); }
    public function create(User $user): bool   { return $user->can('create_project_phase'); }
    public function update(User $user, ProjectPhase $r): bool { return $user->can('update_project_phase'); }
    public function delete(User $user, ProjectPhase $r): bool { return $user->can('delete_project_phase'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_project_phase'); }
}
