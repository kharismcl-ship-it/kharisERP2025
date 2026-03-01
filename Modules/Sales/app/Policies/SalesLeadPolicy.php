<?php

namespace Modules\Sales\Policies;

use App\Models\User;
use Modules\Sales\Models\SalesLead;

class SalesLeadPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_sales_lead'); }
    public function view(User $user, SalesLead $model): bool  { return $user->can('view_sales_lead'); }
    public function create(User $user): bool   { return $user->can('create_sales_lead'); }
    public function update(User $user, SalesLead $model): bool { return $user->can('update_sales_lead'); }
    public function delete(User $user, SalesLead $model): bool { return $user->can('delete_sales_lead'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_sales_lead'); }
}