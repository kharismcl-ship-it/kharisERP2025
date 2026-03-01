<?php

namespace Modules\Sales\Policies;

use App\Models\User;
use Modules\Sales\Models\SalesRestaurant;

class SalesRestaurantPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_sales_restaurant'); }
    public function view(User $user, SalesRestaurant $model): bool  { return $user->can('view_sales_restaurant'); }
    public function create(User $user): bool   { return $user->can('create_sales_restaurant'); }
    public function update(User $user, SalesRestaurant $model): bool { return $user->can('update_sales_restaurant'); }
    public function delete(User $user, SalesRestaurant $model): bool { return $user->can('delete_sales_restaurant'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_sales_restaurant'); }
}