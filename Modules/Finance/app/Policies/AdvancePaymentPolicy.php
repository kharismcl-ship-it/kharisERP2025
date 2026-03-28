<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\AdvancePayment;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdvancePaymentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AdvancePayment');
    }

    public function view(AuthUser $authUser, AdvancePayment $advancePayment): bool
    {
        return $authUser->can('View:AdvancePayment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AdvancePayment');
    }

    public function update(AuthUser $authUser, AdvancePayment $advancePayment): bool
    {
        return $authUser->can('Update:AdvancePayment');
    }

    public function delete(AuthUser $authUser, AdvancePayment $advancePayment): bool
    {
        return $authUser->can('Delete:AdvancePayment');
    }

    public function restore(AuthUser $authUser, AdvancePayment $advancePayment): bool
    {
        return $authUser->can('Restore:AdvancePayment');
    }

    public function forceDelete(AuthUser $authUser, AdvancePayment $advancePayment): bool
    {
        return $authUser->can('ForceDelete:AdvancePayment');
    }
}