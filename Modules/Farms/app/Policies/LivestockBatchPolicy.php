<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\LivestockBatch;
use Illuminate\Auth\Access\HandlesAuthorization;

class LivestockBatchPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LivestockBatch');
    }

    public function view(AuthUser $authUser, LivestockBatch $livestockBatch): bool
    {
        return $authUser->can('View:LivestockBatch');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LivestockBatch');
    }

    public function update(AuthUser $authUser, LivestockBatch $livestockBatch): bool
    {
        return $authUser->can('Update:LivestockBatch');
    }

    public function delete(AuthUser $authUser, LivestockBatch $livestockBatch): bool
    {
        return $authUser->can('Delete:LivestockBatch');
    }

    public function restore(AuthUser $authUser, LivestockBatch $livestockBatch): bool
    {
        return $authUser->can('Restore:LivestockBatch');
    }

    public function forceDelete(AuthUser $authUser, LivestockBatch $livestockBatch): bool
    {
        return $authUser->can('ForceDelete:LivestockBatch');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LivestockBatch');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LivestockBatch');
    }

    public function replicate(AuthUser $authUser, LivestockBatch $livestockBatch): bool
    {
        return $authUser->can('Replicate:LivestockBatch');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LivestockBatch');
    }

}