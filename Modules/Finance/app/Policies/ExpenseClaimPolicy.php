<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\ExpenseClaim;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExpenseClaimPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ExpenseClaim');
    }

    public function view(AuthUser $authUser, ExpenseClaim $expenseClaim): bool
    {
        return $authUser->can('View:ExpenseClaim');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExpenseClaim');
    }

    public function update(AuthUser $authUser, ExpenseClaim $expenseClaim): bool
    {
        return $authUser->can('Update:ExpenseClaim');
    }

    public function delete(AuthUser $authUser, ExpenseClaim $expenseClaim): bool
    {
        return $authUser->can('Delete:ExpenseClaim');
    }

    public function restore(AuthUser $authUser, ExpenseClaim $expenseClaim): bool
    {
        return $authUser->can('Restore:ExpenseClaim');
    }

    public function forceDelete(AuthUser $authUser, ExpenseClaim $expenseClaim): bool
    {
        return $authUser->can('ForceDelete:ExpenseClaim');
    }
}