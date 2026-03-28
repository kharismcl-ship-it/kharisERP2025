<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ProcurementInventory\Models\VendorStatement;

class VendorStatementPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VendorStatement');
    }

    public function view(AuthUser $authUser, VendorStatement $vendorStatement): bool
    {
        return $authUser->can('View:VendorStatement');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VendorStatement');
    }

    public function update(AuthUser $authUser, VendorStatement $vendorStatement): bool
    {
        return $authUser->can('Update:VendorStatement');
    }

    public function delete(AuthUser $authUser, VendorStatement $vendorStatement): bool
    {
        return $authUser->can('Delete:VendorStatement');
    }

    public function restore(AuthUser $authUser, VendorStatement $vendorStatement): bool
    {
        return $authUser->can('Restore:VendorStatement');
    }

    public function forceDelete(AuthUser $authUser, VendorStatement $vendorStatement): bool
    {
        return $authUser->can('ForceDelete:VendorStatement');
    }
}