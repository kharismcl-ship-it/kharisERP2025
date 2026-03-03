<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\FarmMilestone;
use Illuminate\Auth\Access\HandlesAuthorization;

class FarmMilestonePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FarmMilestone');
    }

    public function view(AuthUser $authUser, FarmMilestone $record): bool
    {
        return $authUser->can('View:FarmMilestone');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FarmMilestone');
    }

    public function update(AuthUser $authUser, FarmMilestone $record): bool
    {
        return $authUser->can('Update:FarmMilestone');
    }

    public function delete(AuthUser $authUser, FarmMilestone $record): bool
    {
        return $authUser->can('Delete:FarmMilestone');
    }

    public function restore(AuthUser $authUser, FarmMilestone $record): bool
    {
        return $authUser->can('Restore:FarmMilestone');
    }

    public function forceDelete(AuthUser $authUser, FarmMilestone $record): bool
    {
        return $authUser->can('ForceDelete:FarmMilestone');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FarmMilestone');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FarmMilestone');
    }

    public function replicate(AuthUser $authUser, FarmMilestone $record): bool
    {
        return $authUser->can('Replicate:FarmMilestone');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FarmMilestone');
    }
}
