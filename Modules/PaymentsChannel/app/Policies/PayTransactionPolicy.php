<?php

declare(strict_types=1);

namespace Modules\PaymentsChannel\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\PaymentsChannel\Models\PayTransaction;
use Illuminate\Auth\Access\HandlesAuthorization;

class PayTransactionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PayTransaction');
    }

    public function view(AuthUser $authUser, PayTransaction $payTransaction): bool
    {
        return $authUser->can('View:PayTransaction');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PayTransaction');
    }

    public function update(AuthUser $authUser, PayTransaction $payTransaction): bool
    {
        return $authUser->can('Update:PayTransaction');
    }

    public function delete(AuthUser $authUser, PayTransaction $payTransaction): bool
    {
        return $authUser->can('Delete:PayTransaction');
    }

    public function restore(AuthUser $authUser, PayTransaction $payTransaction): bool
    {
        return $authUser->can('Restore:PayTransaction');
    }

    public function forceDelete(AuthUser $authUser, PayTransaction $payTransaction): bool
    {
        return $authUser->can('ForceDelete:PayTransaction');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PayTransaction');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PayTransaction');
    }

    public function replicate(AuthUser $authUser, PayTransaction $payTransaction): bool
    {
        return $authUser->can('Replicate:PayTransaction');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PayTransaction');
    }

}