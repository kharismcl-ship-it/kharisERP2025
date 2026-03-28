<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\Budget;
use Illuminate\Auth\Access\HandlesAuthorization;

class BudgetPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Budget');
    }

    public function view(AuthUser $authUser, Budget $budget): bool
    {
        return $authUser->can('View:Budget');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Budget');
    }

    public function update(AuthUser $authUser, Budget $budget): bool
    {
        return $authUser->can('Update:Budget');
    }

    public function delete(AuthUser $authUser, Budget $budget): bool
    {
        return $authUser->can('Delete:Budget');
    }

    public function restore(AuthUser $authUser, Budget $budget): bool
    {
        return $authUser->can('Restore:Budget');
    }

    public function forceDelete(AuthUser $authUser, Budget $budget): bool
    {
        return $authUser->can('ForceDelete:Budget');
    }
}