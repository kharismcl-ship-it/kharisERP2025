<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\InvoiceDocument;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoiceDocumentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:InvoiceDocument');
    }

    public function view(AuthUser $authUser, InvoiceDocument $invoiceDocument): bool
    {
        return $authUser->can('View:InvoiceDocument');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:InvoiceDocument');
    }

    public function update(AuthUser $authUser, InvoiceDocument $invoiceDocument): bool
    {
        return $authUser->can('Update:InvoiceDocument');
    }

    public function delete(AuthUser $authUser, InvoiceDocument $invoiceDocument): bool
    {
        return $authUser->can('Delete:InvoiceDocument');
    }

    public function restore(AuthUser $authUser, InvoiceDocument $invoiceDocument): bool
    {
        return $authUser->can('Restore:InvoiceDocument');
    }

    public function forceDelete(AuthUser $authUser, InvoiceDocument $invoiceDocument): bool
    {
        return $authUser->can('ForceDelete:InvoiceDocument');
    }
}