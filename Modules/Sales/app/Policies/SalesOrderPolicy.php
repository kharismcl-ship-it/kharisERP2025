<?php

namespace Modules\Sales\Policies;

use App\Models\User;
use Modules\Sales\Models\SalesOrder;

class SalesOrderPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_sales_order'); }
    public function view(User $user, SalesOrder $model): bool  { return $user->can('view_sales_order'); }
    public function create(User $user): bool   { return $user->can('create_sales_order'); }
    public function update(User $user, SalesOrder $model): bool { return $user->can('update_sales_order'); }
    public function delete(User $user, SalesOrder $model): bool { return $user->can('delete_sales_order'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_sales_order'); }
}