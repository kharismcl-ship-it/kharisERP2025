<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\Cheque;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChequePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Cheque');
    }

    public function view(AuthUser $authUser, Cheque $cheque): bool
    {
        return $authUser->can('View:Cheque');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Cheque');
    }

    public function update(AuthUser $authUser, Cheque $cheque): bool
    {
        return $authUser->can('Update:Cheque');
    }

    public function delete(AuthUser $authUser, Cheque $cheque): bool
    {
        return $authUser->can('Delete:Cheque');
    }

    public function restore(AuthUser $authUser, Cheque $cheque): bool
    {
        return $authUser->can('Restore:Cheque');
    }

    public function forceDelete(AuthUser $authUser, Cheque $cheque): bool
    {
        return $authUser->can('ForceDelete:Cheque');
    }
}