<?php

namespace Modules\Sales\Policies;

use App\Models\User;
use Modules\Sales\Models\SalesCatalog;

class SalesCatalogPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_sales_catalog'); }
    public function view(User $user, SalesCatalog $model): bool  { return $user->can('view_sales_catalog'); }
    public function create(User $user): bool   { return $user->can('create_sales_catalog'); }
    public function update(User $user, SalesCatalog $model): bool { return $user->can('update_sales_catalog'); }
    public function delete(User $user, SalesCatalog $model): bool { return $user->can('delete_sales_catalog'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_sales_catalog'); }
}