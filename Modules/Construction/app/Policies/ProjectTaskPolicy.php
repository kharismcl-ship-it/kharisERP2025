<?php

namespace Modules\Construction\Policies;

use App\Models\User;
use Modules\Construction\Models\ProjectTask;

class ProjectTaskPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_project_task'); }
    public function view(User $user, ProjectTask $r): bool { return $user->can('view_project_task'); }
    public function create(User $user): bool   { return $user->can('create_project_task'); }
    public function update(User $user, ProjectTask $r): bool { return $user->can('update_project_task'); }
    public function delete(User $user, ProjectTask $r): bool { return $user->can('delete_project_task'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_project_task'); }
}
