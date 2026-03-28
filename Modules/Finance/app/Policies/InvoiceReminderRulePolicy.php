<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\InvoiceReminderRule;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoiceReminderRulePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:InvoiceReminderRule');
    }

    public function view(AuthUser $authUser, InvoiceReminderRule $invoiceReminderRule): bool
    {
        return $authUser->can('View:InvoiceReminderRule');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:InvoiceReminderRule');
    }

    public function update(AuthUser $authUser, InvoiceReminderRule $invoiceReminderRule): bool
    {
        return $authUser->can('Update:InvoiceReminderRule');
    }

    public function delete(AuthUser $authUser, InvoiceReminderRule $invoiceReminderRule): bool
    {
        return $authUser->can('Delete:InvoiceReminderRule');
    }

    public function restore(AuthUser $authUser, InvoiceReminderRule $invoiceReminderRule): bool
    {
        return $authUser->can('Restore:InvoiceReminderRule');
    }

    public function forceDelete(AuthUser $authUser, InvoiceReminderRule $invoiceReminderRule): bool
    {
        return $authUser->can('ForceDelete:InvoiceReminderRule');
    }
}