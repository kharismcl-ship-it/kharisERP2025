<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class VendorCatalogPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool { return $authUser->can('ViewAny:VendorCatalog'); }
    public function view(AuthUser $authUser): bool { return $authUser->can('View:VendorCatalog'); }
    public function create(AuthUser $authUser): bool { return $authUser->can('Create:VendorCatalog'); }
    public function update(AuthUser $authUser): bool { return $authUser->can('Update:VendorCatalog'); }
    public function delete(AuthUser $authUser): bool { return $authUser->can('Delete:VendorCatalog'); }
    public function restore(AuthUser $authUser): bool { return $authUser->can('Restore:VendorCatalog'); }
    public function forceDelete(AuthUser $authUser): bool { return $authUser->can('ForceDelete:VendorCatalog'); }
}
