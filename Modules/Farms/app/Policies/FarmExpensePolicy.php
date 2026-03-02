<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\FarmExpense;
use Illuminate\Auth\Access\HandlesAuthorization;

class FarmExpensePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FarmExpense');
    }

    public function view(AuthUser $authUser, FarmExpense $farmExpense): bool
    {
        return $authUser->can('View:FarmExpense');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FarmExpense');
    }

    public function update(AuthUser $authUser, FarmExpense $farmExpense): bool
    {
        return $authUser->can('Update:FarmExpense');
    }

    public function delete(AuthUser $authUser, FarmExpense $farmExpense): bool
    {
        return $authUser->can('Delete:FarmExpense');
    }

    public function restore(AuthUser $authUser, FarmExpense $farmExpense): bool
    {
        return $authUser->can('Restore:FarmExpense');
    }

    public function forceDelete(AuthUser $authUser, FarmExpense $farmExpense): bool
    {
        return $authUser->can('ForceDelete:FarmExpense');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FarmExpense');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FarmExpense');
    }

    public function replicate(AuthUser $authUser, FarmExpense $farmExpense): bool
    {
        return $authUser->can('Replicate:FarmExpense');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FarmExpense');
    }

}