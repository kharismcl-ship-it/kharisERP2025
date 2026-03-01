<?php

namespace Modules\Sales\Policies;

use App\Models\User;
use Modules\Sales\Models\SalesContact;

class SalesContactPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_sales_contact'); }
    public function view(User $user, SalesContact $model): bool  { return $user->can('view_sales_contact'); }
    public function create(User $user): bool   { return $user->can('create_sales_contact'); }
    public function update(User $user, SalesContact $model): bool { return $user->can('update_sales_contact'); }
    public function delete(User $user, SalesContact $model): bool { return $user->can('delete_sales_contact'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_sales_contact'); }
}