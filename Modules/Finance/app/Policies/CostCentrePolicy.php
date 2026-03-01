<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\CostCentre;

class CostCentrePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool { return $authUser->can('ViewAny:CostCentre'); }
    public function view(AuthUser $authUser, CostCentre $model): bool { return $authUser->can('View:CostCentre'); }
    public function create(AuthUser $authUser): bool { return $authUser->can('Create:CostCentre'); }
    public function update(AuthUser $authUser, CostCentre $model): bool { return $authUser->can('Update:CostCentre'); }
    public function delete(AuthUser $authUser, CostCentre $model): bool { return $authUser->can('Delete:CostCentre'); }
    public function restore(AuthUser $authUser, CostCentre $model): bool { return $authUser->can('Restore:CostCentre'); }
    public function forceDelete(AuthUser $authUser, CostCentre $model): bool { return $authUser->can('ForceDelete:CostCentre'); }
    public function forceDeleteAny(AuthUser $authUser): bool { return $authUser->can('ForceDeleteAny:CostCentre'); }
    public function restoreAny(AuthUser $authUser): bool { return $authUser->can('RestoreAny:CostCentre'); }
    public function replicate(AuthUser $authUser, CostCentre $model): bool { return $authUser->can('Replicate:CostCentre'); }
    public function reorder(AuthUser $authUser): bool { return $authUser->can('Reorder:CostCentre'); }
}
