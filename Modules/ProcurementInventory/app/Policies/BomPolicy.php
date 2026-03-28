<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ProcurementInventory\Models\Bom;

class BomPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Bom');
    }

    public function view(AuthUser $authUser, Bom $record): bool
    {
        return $authUser->can('View:Bom');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Bom');
    }

    public function update(AuthUser $authUser, Bom $record): bool
    {
        return $authUser->can('Update:Bom');
    }

    public function delete(AuthUser $authUser, Bom $record): bool
    {
        return $authUser->can('Delete:Bom');
    }

    public function restore(AuthUser $authUser, Bom $record): bool
    {
        return $authUser->can('Restore:Bom');
    }

    public function forceDelete(AuthUser $authUser, Bom $record): bool
    {
        return $authUser->can('ForceDelete:Bom');
    }
}
