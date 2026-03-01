<?php

namespace Modules\Sales\Policies;

use App\Models\User;
use Modules\Sales\Models\SalesOrganization;

class SalesOrganizationPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_sales_organization'); }
    public function view(User $user, SalesOrganization $model): bool  { return $user->can('view_sales_organization'); }
    public function create(User $user): bool   { return $user->can('create_sales_organization'); }
    public function update(User $user, SalesOrganization $model): bool { return $user->can('update_sales_organization'); }
    public function delete(User $user, SalesOrganization $model): bool { return $user->can('delete_sales_organization'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_sales_organization'); }
}