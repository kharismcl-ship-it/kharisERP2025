<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\TaxRate;

class TaxRatePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TaxRate');
    }

    public function view(AuthUser $authUser, TaxRate $taxRate): bool
    {
        return $authUser->can('View:TaxRate');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TaxRate');
    }

    public function update(AuthUser $authUser, TaxRate $taxRate): bool
    {
        return $authUser->can('Update:TaxRate');
    }

    public function delete(AuthUser $authUser, TaxRate $taxRate): bool
    {
        return $authUser->can('Delete:TaxRate');
    }

    public function restore(AuthUser $authUser, TaxRate $taxRate): bool
    {
        return $authUser->can('Restore:TaxRate');
    }

    public function forceDelete(AuthUser $authUser, TaxRate $taxRate): bool
    {
        return $authUser->can('ForceDelete:TaxRate');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TaxRate');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TaxRate');
    }

    public function replicate(AuthUser $authUser, TaxRate $taxRate): bool
    {
        return $authUser->can('Replicate:TaxRate');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TaxRate');
    }
}
