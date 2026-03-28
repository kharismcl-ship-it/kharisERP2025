<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ProcurementInventory\Models\PoChangeOrder;

class PoChangeOrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PoChangeOrder');
    }

    public function view(AuthUser $authUser, PoChangeOrder $record): bool
    {
        return $authUser->can('View:PoChangeOrder');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PoChangeOrder');
    }

    public function update(AuthUser $authUser, PoChangeOrder $record): bool
    {
        return $authUser->can('Update:PoChangeOrder');
    }

    public function delete(AuthUser $authUser, PoChangeOrder $record): bool
    {
        return $authUser->can('Delete:PoChangeOrder');
    }

    public function restore(AuthUser $authUser, PoChangeOrder $record): bool
    {
        return $authUser->can('Restore:PoChangeOrder');
    }

    public function forceDelete(AuthUser $authUser, PoChangeOrder $record): bool
    {
        return $authUser->can('ForceDelete:PoChangeOrder');
    }
}
