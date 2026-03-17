<?php

declare(strict_types=1);

namespace Modules\Construction\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Construction\Models\ProjectPhase;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPhasePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProjectPhase');
    }

    public function view(AuthUser $authUser, ProjectPhase $projectPhase): bool
    {
        return $authUser->can('View:ProjectPhase');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProjectPhase');
    }

    public function update(AuthUser $authUser, ProjectPhase $projectPhase): bool
    {
        return $authUser->can('Update:ProjectPhase');
    }

    public function delete(AuthUser $authUser, ProjectPhase $projectPhase): bool
    {
        return $authUser->can('Delete:ProjectPhase');
    }

    public function restore(AuthUser $authUser, ProjectPhase $projectPhase): bool
    {
        return $authUser->can('Restore:ProjectPhase');
    }

    public function forceDelete(AuthUser $authUser, ProjectPhase $projectPhase): bool
    {
        return $authUser->can('ForceDelete:ProjectPhase');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProjectPhase');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProjectPhase');
    }

    public function replicate(AuthUser $authUser, ProjectPhase $projectPhase): bool
    {
        return $authUser->can('Replicate:ProjectPhase');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProjectPhase');
    }

}