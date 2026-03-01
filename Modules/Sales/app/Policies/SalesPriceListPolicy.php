<?php

namespace Modules\Sales\Policies;

use App\Models\User;
use Modules\Sales\Models\SalesPriceList;

class SalesPriceListPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_sales_price_list'); }
    public function view(User $user, SalesPriceList $model): bool  { return $user->can('view_sales_price_list'); }
    public function create(User $user): bool   { return $user->can('create_sales_price_list'); }
    public function update(User $user, SalesPriceList $model): bool { return $user->can('update_sales_price_list'); }
    public function delete(User $user, SalesPriceList $model): bool { return $user->can('delete_sales_price_list'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_sales_price_list'); }
}