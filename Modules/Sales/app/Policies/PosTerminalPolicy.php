<?php

namespace Modules\Sales\Policies;

use App\Models\User;
use Modules\Sales\Models\PosTerminal;

class PosTerminalPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_pos_terminal'); }
    public function view(User $user, PosTerminal $model): bool  { return $user->can('view_pos_terminal'); }
    public function create(User $user): bool   { return $user->can('create_pos_terminal'); }
    public function update(User $user, PosTerminal $model): bool { return $user->can('update_pos_terminal'); }
    public function delete(User $user, PosTerminal $model): bool { return $user->can('delete_pos_terminal'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_pos_terminal'); }
}