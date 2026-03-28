<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class RtvOrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool { return $authUser->can('ViewAny:RtvOrder'); }
    public function view(AuthUser $authUser): bool { return $authUser->can('View:RtvOrder'); }
    public function create(AuthUser $authUser): bool { return $authUser->can('Create:RtvOrder'); }
    public function update(AuthUser $authUser): bool { return $authUser->can('Update:RtvOrder'); }
    public function delete(AuthUser $authUser): bool { return $authUser->can('Delete:RtvOrder'); }
    public function restore(AuthUser $authUser): bool { return $authUser->can('Restore:RtvOrder'); }
    public function forceDelete(AuthUser $authUser): bool { return $authUser->can('ForceDelete:RtvOrder'); }
}
