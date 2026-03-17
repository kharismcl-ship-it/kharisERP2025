<?php

declare(strict_types=1);

namespace Modules\Construction\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Construction\Models\ConstructionDocument;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConstructionDocumentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ConstructionDocument');
    }

    public function view(AuthUser $authUser, ConstructionDocument $constructionDocument): bool
    {
        return $authUser->can('View:ConstructionDocument');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ConstructionDocument');
    }

    public function update(AuthUser $authUser, ConstructionDocument $constructionDocument): bool
    {
        return $authUser->can('Update:ConstructionDocument');
    }

    public function delete(AuthUser $authUser, ConstructionDocument $constructionDocument): bool
    {
        return $authUser->can('Delete:ConstructionDocument');
    }

    public function restore(AuthUser $authUser, ConstructionDocument $constructionDocument): bool
    {
        return $authUser->can('Restore:ConstructionDocument');
    }

    public function forceDelete(AuthUser $authUser, ConstructionDocument $constructionDocument): bool
    {
        return $authUser->can('ForceDelete:ConstructionDocument');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ConstructionDocument');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ConstructionDocument');
    }

    public function replicate(AuthUser $authUser, ConstructionDocument $constructionDocument): bool
    {
        return $authUser->can('Replicate:ConstructionDocument');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ConstructionDocument');
    }

}