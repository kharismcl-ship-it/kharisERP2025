<?php

declare(strict_types=1);

namespace Modules\Construction\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Construction\Models\ConstructionProject;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConstructionProjectPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ConstructionProject');
    }

    public function view(AuthUser $authUser, ConstructionProject $constructionProject): bool
    {
        return $authUser->can('View:ConstructionProject');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ConstructionProject');
    }

    public function update(AuthUser $authUser, ConstructionProject $constructionProject): bool
    {
        return $authUser->can('Update:ConstructionProject');
    }

    public function delete(AuthUser $authUser, ConstructionProject $constructionProject): bool
    {
        return $authUser->can('Delete:ConstructionProject');
    }

    public function restore(AuthUser $authUser, ConstructionProject $constructionProject): bool
    {
        return $authUser->can('Restore:ConstructionProject');
    }

    public function forceDelete(AuthUser $authUser, ConstructionProject $constructionProject): bool
    {
        return $authUser->can('ForceDelete:ConstructionProject');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ConstructionProject');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ConstructionProject');
    }

    public function replicate(AuthUser $authUser, ConstructionProject $constructionProject): bool
    {
        return $authUser->can('Replicate:ConstructionProject');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ConstructionProject');
    }

}