<?php

namespace Modules\Sales\Policies;

use App\Models\User;
use Modules\Sales\Models\SalesQuotation;

class SalesQuotationPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_sales_quotation'); }
    public function view(User $user, SalesQuotation $model): bool  { return $user->can('view_sales_quotation'); }
    public function create(User $user): bool   { return $user->can('create_sales_quotation'); }
    public function update(User $user, SalesQuotation $model): bool { return $user->can('update_sales_quotation'); }
    public function delete(User $user, SalesQuotation $model): bool { return $user->can('delete_sales_quotation'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_sales_quotation'); }
}