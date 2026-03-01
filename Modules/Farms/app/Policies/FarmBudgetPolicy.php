<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\FarmBudget;

class FarmBudgetPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_farm_budget'); }
    public function view(User $user, FarmBudget $record): bool { return $user->can('view_farm_budget'); }
    public function create(User $user): bool   { return $user->can('create_farm_budget'); }
    public function update(User $user, FarmBudget $record): bool { return $user->can('update_farm_budget'); }
    public function delete(User $user, FarmBudget $record): bool { return $user->can('delete_farm_budget'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_farm_budget'); }
}