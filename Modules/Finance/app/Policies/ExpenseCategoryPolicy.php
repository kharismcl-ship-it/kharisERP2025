<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\ExpenseCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExpenseCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ExpenseCategory');
    }

    public function view(AuthUser $authUser, ExpenseCategory $expenseCategory): bool
    {
        return $authUser->can('View:ExpenseCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExpenseCategory');
    }

    public function update(AuthUser $authUser, ExpenseCategory $expenseCategory): bool
    {
        return $authUser->can('Update:ExpenseCategory');
    }

    public function delete(AuthUser $authUser, ExpenseCategory $expenseCategory): bool
    {
        return $authUser->can('Delete:ExpenseCategory');
    }

    public function restore(AuthUser $authUser, ExpenseCategory $expenseCategory): bool
    {
        return $authUser->can('Restore:ExpenseCategory');
    }

    public function forceDelete(AuthUser $authUser, ExpenseCategory $expenseCategory): bool
    {
        return $authUser->can('ForceDelete:ExpenseCategory');
    }
}