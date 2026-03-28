<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ProcurementInventory\Models\LandedCost;

class LandedCostPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LandedCost');
    }

    public function view(AuthUser $authUser, LandedCost $record): bool
    {
        return $authUser->can('View:LandedCost');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LandedCost');
    }

    public function update(AuthUser $authUser, LandedCost $record): bool
    {
        return $authUser->can('Update:LandedCost');
    }

    public function delete(AuthUser $authUser, LandedCost $record): bool
    {
        return $authUser->can('Delete:LandedCost');
    }

    public function restore(AuthUser $authUser, LandedCost $record): bool
    {
        return $authUser->can('Restore:LandedCost');
    }

    public function forceDelete(AuthUser $authUser, LandedCost $record): bool
    {
        return $authUser->can('ForceDelete:LandedCost');
    }
}
