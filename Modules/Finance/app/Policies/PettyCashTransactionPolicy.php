<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\PettyCashTransaction;
use Illuminate\Auth\Access\HandlesAuthorization;

class PettyCashTransactionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PettyCashTransaction');
    }

    public function view(AuthUser $authUser, PettyCashTransaction $pettyCashTransaction): bool
    {
        return $authUser->can('View:PettyCashTransaction');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PettyCashTransaction');
    }

    public function update(AuthUser $authUser, PettyCashTransaction $pettyCashTransaction): bool
    {
        return $authUser->can('Update:PettyCashTransaction');
    }

    public function delete(AuthUser $authUser, PettyCashTransaction $pettyCashTransaction): bool
    {
        return $authUser->can('Delete:PettyCashTransaction');
    }

    public function restore(AuthUser $authUser, PettyCashTransaction $pettyCashTransaction): bool
    {
        return $authUser->can('Restore:PettyCashTransaction');
    }

    public function forceDelete(AuthUser $authUser, PettyCashTransaction $pettyCashTransaction): bool
    {
        return $authUser->can('ForceDelete:PettyCashTransaction');
    }
}