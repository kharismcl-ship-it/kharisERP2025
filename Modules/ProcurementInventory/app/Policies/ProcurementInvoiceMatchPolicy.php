<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ProcurementInvoiceMatchPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool { return $authUser->can('ViewAny:ProcurementInvoiceMatch'); }
    public function view(AuthUser $authUser): bool { return $authUser->can('View:ProcurementInvoiceMatch'); }
    public function create(AuthUser $authUser): bool { return $authUser->can('Create:ProcurementInvoiceMatch'); }
    public function update(AuthUser $authUser): bool { return $authUser->can('Update:ProcurementInvoiceMatch'); }
    public function delete(AuthUser $authUser): bool { return $authUser->can('Delete:ProcurementInvoiceMatch'); }
    public function restore(AuthUser $authUser): bool { return $authUser->can('Restore:ProcurementInvoiceMatch'); }
    public function forceDelete(AuthUser $authUser): bool { return $authUser->can('ForceDelete:ProcurementInvoiceMatch'); }
}
