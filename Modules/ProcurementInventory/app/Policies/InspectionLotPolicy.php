<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class InspectionLotPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool { return $authUser->can('ViewAny:InspectionLot'); }
    public function view(AuthUser $authUser): bool { return $authUser->can('View:InspectionLot'); }
    public function create(AuthUser $authUser): bool { return $authUser->can('Create:InspectionLot'); }
    public function update(AuthUser $authUser): bool { return $authUser->can('Update:InspectionLot'); }
    public function delete(AuthUser $authUser): bool { return $authUser->can('Delete:InspectionLot'); }
    public function restore(AuthUser $authUser): bool { return $authUser->can('Restore:InspectionLot'); }
    public function forceDelete(AuthUser $authUser): bool { return $authUser->can('ForceDelete:InspectionLot'); }
}
