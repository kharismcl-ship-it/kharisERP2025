<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\Farm;

class FarmPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_farm'); }
    public function view(User $user, Farm $r): bool { return $user->can('view_farm'); }
    public function create(User $user): bool   { return $user->can('create_farm'); }
    public function update(User $user, Farm $r): bool { return $user->can('update_farm'); }
    public function delete(User $user, Farm $r): bool { return $user->can('delete_farm'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_farm'); }
}
