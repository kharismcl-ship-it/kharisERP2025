<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\PaymentBatch;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentBatchPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PaymentBatch');
    }

    public function view(AuthUser $authUser, PaymentBatch $paymentBatch): bool
    {
        return $authUser->can('View:PaymentBatch');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PaymentBatch');
    }

    public function update(AuthUser $authUser, PaymentBatch $paymentBatch): bool
    {
        return $authUser->can('Update:PaymentBatch');
    }

    public function delete(AuthUser $authUser, PaymentBatch $paymentBatch): bool
    {
        return $authUser->can('Delete:PaymentBatch');
    }

    public function restore(AuthUser $authUser, PaymentBatch $paymentBatch): bool
    {
        return $authUser->can('Restore:PaymentBatch');
    }

    public function forceDelete(AuthUser $authUser, PaymentBatch $paymentBatch): bool
    {
        return $authUser->can('ForceDelete:PaymentBatch');
    }
}