<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\FarmExpense;

class FarmExpensePolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_farm_expense'); }
    public function view(User $user, FarmExpense $r): bool { return $user->can('view_farm_expense'); }
    public function create(User $user): bool   { return $user->can('create_farm_expense'); }
    public function update(User $user, FarmExpense $r): bool { return $user->can('update_farm_expense'); }
    public function delete(User $user, FarmExpense $r): bool { return $user->can('delete_farm_expense'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_farm_expense'); }
}
