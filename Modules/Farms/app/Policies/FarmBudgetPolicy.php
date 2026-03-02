<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\FarmBudget;
use Illuminate\Auth\Access\HandlesAuthorization;

class FarmBudgetPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FarmBudget');
    }

    public function view(AuthUser $authUser, FarmBudget $farmBudget): bool
    {
        return $authUser->can('View:FarmBudget');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FarmBudget');
    }

    public function update(AuthUser $authUser, FarmBudget $farmBudget): bool
    {
        return $authUser->can('Update:FarmBudget');
    }

    public function delete(AuthUser $authUser, FarmBudget $farmBudget): bool
    {
        return $authUser->can('Delete:FarmBudget');
    }

    public function restore(AuthUser $authUser, FarmBudget $farmBudget): bool
    {
        return $authUser->can('Restore:FarmBudget');
    }

    public function forceDelete(AuthUser $authUser, FarmBudget $farmBudget): bool
    {
        return $authUser->can('ForceDelete:FarmBudget');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FarmBudget');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FarmBudget');
    }

    public function replicate(AuthUser $authUser, FarmBudget $farmBudget): bool
    {
        return $authUser->can('Replicate:FarmBudget');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FarmBudget');
    }

}