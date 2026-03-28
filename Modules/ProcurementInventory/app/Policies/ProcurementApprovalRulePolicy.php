<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ProcurementInventory\Models\ProcurementApprovalRule;

class ProcurementApprovalRulePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool { return $authUser->can('ViewAny:ProcurementApprovalRule'); }
    public function view(AuthUser $authUser, ProcurementApprovalRule $model): bool { return $authUser->can('View:ProcurementApprovalRule'); }
    public function create(AuthUser $authUser): bool { return $authUser->can('Create:ProcurementApprovalRule'); }
    public function update(AuthUser $authUser, ProcurementApprovalRule $model): bool { return $authUser->can('Update:ProcurementApprovalRule'); }
    public function delete(AuthUser $authUser, ProcurementApprovalRule $model): bool { return $authUser->can('Delete:ProcurementApprovalRule'); }
    public function restore(AuthUser $authUser, ProcurementApprovalRule $model): bool { return $authUser->can('Restore:ProcurementApprovalRule'); }
    public function forceDelete(AuthUser $authUser, ProcurementApprovalRule $model): bool { return $authUser->can('ForceDelete:ProcurementApprovalRule'); }
}
