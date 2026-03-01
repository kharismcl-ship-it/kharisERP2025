<?php

namespace Modules\Sales\Policies;

use App\Models\User;
use Modules\Sales\Models\SalesOpportunity;

class SalesOpportunityPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_sales_opportunity'); }
    public function view(User $user, SalesOpportunity $model): bool  { return $user->can('view_sales_opportunity'); }
    public function create(User $user): bool   { return $user->can('create_sales_opportunity'); }
    public function update(User $user, SalesOpportunity $model): bool { return $user->can('update_sales_opportunity'); }
    public function delete(User $user, SalesOpportunity $model): bool { return $user->can('delete_sales_opportunity'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_sales_opportunity'); }
}