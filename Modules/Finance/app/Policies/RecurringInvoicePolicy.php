<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\RecurringInvoice;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecurringInvoicePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RecurringInvoice');
    }

    public function view(AuthUser $authUser, RecurringInvoice $recurringInvoice): bool
    {
        return $authUser->can('View:RecurringInvoice');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RecurringInvoice');
    }

    public function update(AuthUser $authUser, RecurringInvoice $recurringInvoice): bool
    {
        return $authUser->can('Update:RecurringInvoice');
    }

    public function delete(AuthUser $authUser, RecurringInvoice $recurringInvoice): bool
    {
        return $authUser->can('Delete:RecurringInvoice');
    }

    public function restore(AuthUser $authUser, RecurringInvoice $recurringInvoice): bool
    {
        return $authUser->can('Restore:RecurringInvoice');
    }

    public function forceDelete(AuthUser $authUser, RecurringInvoice $recurringInvoice): bool
    {
        return $authUser->can('ForceDelete:RecurringInvoice');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RecurringInvoice');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RecurringInvoice');
    }

    public function replicate(AuthUser $authUser, RecurringInvoice $recurringInvoice): bool
    {
        return $authUser->can('Replicate:RecurringInvoice');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RecurringInvoice');
    }

}