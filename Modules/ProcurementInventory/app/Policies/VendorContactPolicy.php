<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ProcurementInventory\Models\VendorContact;
use Illuminate\Auth\Access\HandlesAuthorization;

class VendorContactPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VendorContact');
    }

    public function view(AuthUser $authUser, VendorContact $vendorContact): bool
    {
        return $authUser->can('View:VendorContact');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VendorContact');
    }

    public function update(AuthUser $authUser, VendorContact $vendorContact): bool
    {
        return $authUser->can('Update:VendorContact');
    }

    public function delete(AuthUser $authUser, VendorContact $vendorContact): bool
    {
        return $authUser->can('Delete:VendorContact');
    }

    public function restore(AuthUser $authUser, VendorContact $vendorContact): bool
    {
        return $authUser->can('Restore:VendorContact');
    }

    public function forceDelete(AuthUser $authUser, VendorContact $vendorContact): bool
    {
        return $authUser->can('ForceDelete:VendorContact');
    }

    public function replicate(AuthUser $authUser, VendorContact $vendorContact): bool
    {
        return $authUser->can('Replicate:VendorContact');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:VendorContact');
    }
}
