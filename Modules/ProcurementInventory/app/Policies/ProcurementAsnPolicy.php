<?php

declare(strict_types=1);

namespace Modules\ProcurementInventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ProcurementInventory\Models\ProcurementAsn;

class ProcurementAsnPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProcurementAsn');
    }

    public function view(AuthUser $authUser, ProcurementAsn $record): bool
    {
        return $authUser->can('View:ProcurementAsn');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProcurementAsn');
    }

    public function update(AuthUser $authUser, ProcurementAsn $record): bool
    {
        return $authUser->can('Update:ProcurementAsn');
    }

    public function delete(AuthUser $authUser, ProcurementAsn $record): bool
    {
        return $authUser->can('Delete:ProcurementAsn');
    }

    public function restore(AuthUser $authUser, ProcurementAsn $record): bool
    {
        return $authUser->can('Restore:ProcurementAsn');
    }

    public function forceDelete(AuthUser $authUser, ProcurementAsn $record): bool
    {
        return $authUser->can('ForceDelete:ProcurementAsn');
    }
}
