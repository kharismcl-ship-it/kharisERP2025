<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\InvoiceLine;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoiceLinePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:InvoiceLine');
    }

    public function view(AuthUser $authUser, InvoiceLine $invoiceLine): bool
    {
        return $authUser->can('View:InvoiceLine');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:InvoiceLine');
    }

    public function update(AuthUser $authUser, InvoiceLine $invoiceLine): bool
    {
        return $authUser->can('Update:InvoiceLine');
    }

    public function delete(AuthUser $authUser, InvoiceLine $invoiceLine): bool
    {
        return $authUser->can('Delete:InvoiceLine');
    }

    public function restore(AuthUser $authUser, InvoiceLine $invoiceLine): bool
    {
        return $authUser->can('Restore:InvoiceLine');
    }

    public function forceDelete(AuthUser $authUser, InvoiceLine $invoiceLine): bool
    {
        return $authUser->can('ForceDelete:InvoiceLine');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:InvoiceLine');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:InvoiceLine');
    }

    public function replicate(AuthUser $authUser, InvoiceLine $invoiceLine): bool
    {
        return $authUser->can('Replicate:InvoiceLine');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:InvoiceLine');
    }

}