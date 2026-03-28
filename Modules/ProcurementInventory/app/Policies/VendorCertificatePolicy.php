<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class VendorCertificatePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool { return $authUser->can('ViewAny:VendorCertificate'); }
    public function view(AuthUser $authUser): bool { return $authUser->can('View:VendorCertificate'); }
    public function create(AuthUser $authUser): bool { return $authUser->can('Create:VendorCertificate'); }
    public function update(AuthUser $authUser): bool { return $authUser->can('Update:VendorCertificate'); }
    public function delete(AuthUser $authUser): bool { return $authUser->can('Delete:VendorCertificate'); }
    public function restore(AuthUser $authUser): bool { return $authUser->can('Restore:VendorCertificate'); }
    public function forceDelete(AuthUser $authUser): bool { return $authUser->can('ForceDelete:VendorCertificate'); }
}
