<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\PaymentAllocation;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentAllocationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PaymentAllocation');
    }

    public function view(AuthUser $authUser, PaymentAllocation $paymentAllocation): bool
    {
        return $authUser->can('View:PaymentAllocation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PaymentAllocation');
    }

    public function update(AuthUser $authUser, PaymentAllocation $paymentAllocation): bool
    {
        return $authUser->can('Update:PaymentAllocation');
    }

    public function delete(AuthUser $authUser, PaymentAllocation $paymentAllocation): bool
    {
        return $authUser->can('Delete:PaymentAllocation');
    }

    public function restore(AuthUser $authUser, PaymentAllocation $paymentAllocation): bool
    {
        return $authUser->can('Restore:PaymentAllocation');
    }

    public function forceDelete(AuthUser $authUser, PaymentAllocation $paymentAllocation): bool
    {
        return $authUser->can('ForceDelete:PaymentAllocation');
    }
}