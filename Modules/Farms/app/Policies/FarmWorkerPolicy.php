<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\FarmWorker;
use Illuminate\Auth\Access\HandlesAuthorization;

class FarmWorkerPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FarmWorker');
    }

    public function view(AuthUser $authUser, FarmWorker $farmWorker): bool
    {
        return $authUser->can('View:FarmWorker');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FarmWorker');
    }

    public function update(AuthUser $authUser, FarmWorker $farmWorker): bool
    {
        return $authUser->can('Update:FarmWorker');
    }

    public function delete(AuthUser $authUser, FarmWorker $farmWorker): bool
    {
        return $authUser->can('Delete:FarmWorker');
    }

    public function restore(AuthUser $authUser, FarmWorker $farmWorker): bool
    {
        return $authUser->can('Restore:FarmWorker');
    }

    public function forceDelete(AuthUser $authUser, FarmWorker $farmWorker): bool
    {
        return $authUser->can('ForceDelete:FarmWorker');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FarmWorker');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FarmWorker');
    }

    public function replicate(AuthUser $authUser, FarmWorker $farmWorker): bool
    {
        return $authUser->can('Replicate:FarmWorker');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FarmWorker');
    }

}