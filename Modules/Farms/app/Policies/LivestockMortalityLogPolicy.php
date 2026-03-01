<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\LivestockMortalityLog;

class LivestockMortalityLogPolicy
{
    public function viewAny(User $user): bool   { return $user->can('view_any_livestock_mortality_log'); }
    public function view(User $user, LivestockMortalityLog $r): bool { return $user->can('view_livestock_mortality_log'); }
    public function create(User $user): bool    { return $user->can('create_livestock_mortality_log'); }
    public function update(User $user, LivestockMortalityLog $r): bool { return $user->can('update_livestock_mortality_log'); }
    public function delete(User $user, LivestockMortalityLog $r): bool { return $user->can('delete_livestock_mortality_log'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_livestock_mortality_log'); }
}