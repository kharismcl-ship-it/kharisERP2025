<?php

namespace Modules\Sales\Policies;

use App\Models\User;
use Modules\Sales\Models\SalesActivity;

class SalesActivityPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_sales_activity'); }
    public function view(User $user, SalesActivity $model): bool  { return $user->can('view_sales_activity'); }
    public function create(User $user): bool   { return $user->can('create_sales_activity'); }
    public function update(User $user, SalesActivity $model): bool { return $user->can('update_sales_activity'); }
    public function delete(User $user, SalesActivity $model): bool { return $user->can('delete_sales_activity'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_sales_activity'); }
}