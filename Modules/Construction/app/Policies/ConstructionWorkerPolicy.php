<?php

declare(strict_types=1);

namespace Modules\Construction\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Construction\Models\ConstructionWorker;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConstructionWorkerPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ConstructionWorker');
    }

    public function view(AuthUser $authUser, ConstructionWorker $constructionWorker): bool
    {
        return $authUser->can('View:ConstructionWorker');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ConstructionWorker');
    }

    public function update(AuthUser $authUser, ConstructionWorker $constructionWorker): bool
    {
        return $authUser->can('Update:ConstructionWorker');
    }

    public function delete(AuthUser $authUser, ConstructionWorker $constructionWorker): bool
    {
        return $authUser->can('Delete:ConstructionWorker');
    }

    public function restore(AuthUser $authUser, ConstructionWorker $constructionWorker): bool
    {
        return $authUser->can('Restore:ConstructionWorker');
    }

    public function forceDelete(AuthUser $authUser, ConstructionWorker $constructionWorker): bool
    {
        return $authUser->can('ForceDelete:ConstructionWorker');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ConstructionWorker');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ConstructionWorker');
    }

    public function replicate(AuthUser $authUser, ConstructionWorker $constructionWorker): bool
    {
        return $authUser->can('Replicate:ConstructionWorker');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ConstructionWorker');
    }

}