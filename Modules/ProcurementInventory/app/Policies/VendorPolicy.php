<?php

namespace Modules\ProcurementInventory\Policies;

use App\Models\User;
use Modules\ProcurementInventory\Models\Vendor;

class VendorPolicy
{
    public function viewAny(User $user): bool { return $user->can('view_any_vendor'); }
    public function view(User $user, Vendor $model): bool { return $user->can('view_vendor'); }
    public function create(User $user): bool { return $user->can('create_vendor'); }
    public function update(User $user, Vendor $model): bool { return $user->can('update_vendor'); }
    public function delete(User $user, Vendor $model): bool { return $user->can('delete_vendor'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_vendor'); }
}