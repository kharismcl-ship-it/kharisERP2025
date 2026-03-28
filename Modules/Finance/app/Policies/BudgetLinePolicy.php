<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\BudgetLine;
use Illuminate\Auth\Access\HandlesAuthorization;

class BudgetLinePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BudgetLine');
    }

    public function view(AuthUser $authUser, BudgetLine $budgetLine): bool
    {
        return $authUser->can('View:BudgetLine');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BudgetLine');
    }

    public function update(AuthUser $authUser, BudgetLine $budgetLine): bool
    {
        return $authUser->can('Update:BudgetLine');
    }

    public function delete(AuthUser $authUser, BudgetLine $budgetLine): bool
    {
        return $authUser->can('Delete:BudgetLine');
    }

    public function restore(AuthUser $authUser, BudgetLine $budgetLine): bool
    {
        return $authUser->can('Restore:BudgetLine');
    }

    public function forceDelete(AuthUser $authUser, BudgetLine $budgetLine): bool
    {
        return $authUser->can('ForceDelete:BudgetLine');
    }
}