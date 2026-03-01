<?php

namespace Modules\Sales\Policies;

use App\Models\User;
use Modules\Sales\Models\PosSession;

class PosSessionPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_pos_session'); }
    public function view(User $user, PosSession $model): bool  { return $user->can('view_pos_session'); }
    public function create(User $user): bool   { return $user->can('create_pos_session'); }
    public function update(User $user, PosSession $model): bool { return $user->can('update_pos_session'); }
    public function delete(User $user, PosSession $model): bool { return $user->can('delete_pos_session'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_pos_session'); }
}