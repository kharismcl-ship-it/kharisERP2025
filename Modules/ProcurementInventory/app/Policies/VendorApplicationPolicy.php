<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class VendorApplicationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool { return $authUser->can('ViewAny:VendorApplication'); }
    public function view(AuthUser $authUser): bool { return $authUser->can('View:VendorApplication'); }
    public function create(AuthUser $authUser): bool { return $authUser->can('Create:VendorApplication'); }
    public function update(AuthUser $authUser): bool { return $authUser->can('Update:VendorApplication'); }
    public function delete(AuthUser $authUser): bool { return $authUser->can('Delete:VendorApplication'); }
    public function restore(AuthUser $authUser): bool { return $authUser->can('Restore:VendorApplication'); }
    public function forceDelete(AuthUser $authUser): bool { return $authUser->can('ForceDelete:VendorApplication'); }
}
