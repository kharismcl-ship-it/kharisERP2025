<?php

declare(strict_types=1);

namespace Modules\Construction\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Construction\Models\ProjectBudgetItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectBudgetItemPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProjectBudgetItem');
    }

    public function view(AuthUser $authUser, ProjectBudgetItem $projectBudgetItem): bool
    {
        return $authUser->can('View:ProjectBudgetItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProjectBudgetItem');
    }

    public function update(AuthUser $authUser, ProjectBudgetItem $projectBudgetItem): bool
    {
        return $authUser->can('Update:ProjectBudgetItem');
    }

    public function delete(AuthUser $authUser, ProjectBudgetItem $projectBudgetItem): bool
    {
        return $authUser->can('Delete:ProjectBudgetItem');
    }

    public function restore(AuthUser $authUser, ProjectBudgetItem $projectBudgetItem): bool
    {
        return $authUser->can('Restore:ProjectBudgetItem');
    }

    public function forceDelete(AuthUser $authUser, ProjectBudgetItem $projectBudgetItem): bool
    {
        return $authUser->can('ForceDelete:ProjectBudgetItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProjectBudgetItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProjectBudgetItem');
    }

    public function replicate(AuthUser $authUser, ProjectBudgetItem $projectBudgetItem): bool
    {
        return $authUser->can('Replicate:ProjectBudgetItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProjectBudgetItem');
    }

}