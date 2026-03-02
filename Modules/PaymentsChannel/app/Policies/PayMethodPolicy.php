<?php

declare(strict_types=1);

namespace Modules\PaymentsChannel\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\PaymentsChannel\Models\PayMethod;
use Illuminate\Auth\Access\HandlesAuthorization;

class PayMethodPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PayMethod');
    }

    public function view(AuthUser $authUser, PayMethod $payMethod): bool
    {
        return $authUser->can('View:PayMethod');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PayMethod');
    }

    public function update(AuthUser $authUser, PayMethod $payMethod): bool
    {
        return $authUser->can('Update:PayMethod');
    }

    public function delete(AuthUser $authUser, PayMethod $payMethod): bool
    {
        return $authUser->can('Delete:PayMethod');
    }

    public function restore(AuthUser $authUser, PayMethod $payMethod): bool
    {
        return $authUser->can('Restore:PayMethod');
    }

    public function forceDelete(AuthUser $authUser, PayMethod $payMethod): bool
    {
        return $authUser->can('ForceDelete:PayMethod');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PayMethod');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PayMethod');
    }

    public function replicate(AuthUser $authUser, PayMethod $payMethod): bool
    {
        return $authUser->can('Replicate:PayMethod');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PayMethod');
    }

}