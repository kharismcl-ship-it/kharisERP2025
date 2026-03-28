<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class VendorPerformancePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool { return $authUser->can('ViewAny:VendorPerformance'); }
    public function view(AuthUser $authUser): bool { return $authUser->can('View:VendorPerformance'); }
    public function create(AuthUser $authUser): bool { return $authUser->can('Create:VendorPerformance'); }
    public function update(AuthUser $authUser): bool { return $authUser->can('Update:VendorPerformance'); }
    public function delete(AuthUser $authUser): bool { return $authUser->can('Delete:VendorPerformance'); }
    public function restore(AuthUser $authUser): bool { return $authUser->can('Restore:VendorPerformance'); }
    public function forceDelete(AuthUser $authUser): bool { return $authUser->can('ForceDelete:VendorPerformance'); }
}
