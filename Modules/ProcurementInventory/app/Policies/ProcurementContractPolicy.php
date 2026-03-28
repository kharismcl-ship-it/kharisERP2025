<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ProcurementContractPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool { return $authUser->can('ViewAny:ProcurementContract'); }
    public function view(AuthUser $authUser): bool { return $authUser->can('View:ProcurementContract'); }
    public function create(AuthUser $authUser): bool { return $authUser->can('Create:ProcurementContract'); }
    public function update(AuthUser $authUser): bool { return $authUser->can('Update:ProcurementContract'); }
    public function delete(AuthUser $authUser): bool { return $authUser->can('Delete:ProcurementContract'); }
    public function restore(AuthUser $authUser): bool { return $authUser->can('Restore:ProcurementContract'); }
    public function forceDelete(AuthUser $authUser): bool { return $authUser->can('ForceDelete:ProcurementContract'); }
}
