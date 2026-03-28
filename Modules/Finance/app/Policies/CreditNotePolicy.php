<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\CreditNote;
use Illuminate\Auth\Access\HandlesAuthorization;

class CreditNotePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CreditNote');
    }

    public function view(AuthUser $authUser, CreditNote $creditNote): bool
    {
        return $authUser->can('View:CreditNote');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CreditNote');
    }

    public function update(AuthUser $authUser, CreditNote $creditNote): bool
    {
        return $authUser->can('Update:CreditNote');
    }

    public function delete(AuthUser $authUser, CreditNote $creditNote): bool
    {
        return $authUser->can('Delete:CreditNote');
    }

    public function restore(AuthUser $authUser, CreditNote $creditNote): bool
    {
        return $authUser->can('Restore:CreditNote');
    }

    public function forceDelete(AuthUser $authUser, CreditNote $creditNote): bool
    {
        return $authUser->can('ForceDelete:CreditNote');
    }
}