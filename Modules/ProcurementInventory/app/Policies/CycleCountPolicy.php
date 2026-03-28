<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ProcurementInventory\Models\CycleCount;

class CycleCountPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CycleCount');
    }

    public function view(AuthUser $authUser, CycleCount $record): bool
    {
        return $authUser->can('View:CycleCount');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CycleCount');
    }

    public function update(AuthUser $authUser, CycleCount $record): bool
    {
        return $authUser->can('Update:CycleCount');
    }

    public function delete(AuthUser $authUser, CycleCount $record): bool
    {
        return $authUser->can('Delete:CycleCount');
    }

    public function restore(AuthUser $authUser, CycleCount $record): bool
    {
        return $authUser->can('Restore:CycleCount');
    }

    public function forceDelete(AuthUser $authUser, CycleCount $record): bool
    {
        return $authUser->can('ForceDelete:CycleCount');
    }
}
