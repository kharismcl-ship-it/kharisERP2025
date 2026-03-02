<?php

declare(strict_types=1);

namespace Modules\ManufacturingPaper\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ManufacturingPaper\Models\MpProductionBatch;
use Illuminate\Auth\Access\HandlesAuthorization;

class MpProductionBatchPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MpProductionBatch');
    }

    public function view(AuthUser $authUser, MpProductionBatch $mpProductionBatch): bool
    {
        return $authUser->can('View:MpProductionBatch');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MpProductionBatch');
    }

    public function update(AuthUser $authUser, MpProductionBatch $mpProductionBatch): bool
    {
        return $authUser->can('Update:MpProductionBatch');
    }

    public function delete(AuthUser $authUser, MpProductionBatch $mpProductionBatch): bool
    {
        return $authUser->can('Delete:MpProductionBatch');
    }

    public function restore(AuthUser $authUser, MpProductionBatch $mpProductionBatch): bool
    {
        return $authUser->can('Restore:MpProductionBatch');
    }

    public function forceDelete(AuthUser $authUser, MpProductionBatch $mpProductionBatch): bool
    {
        return $authUser->can('ForceDelete:MpProductionBatch');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MpProductionBatch');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MpProductionBatch');
    }

    public function replicate(AuthUser $authUser, MpProductionBatch $mpProductionBatch): bool
    {
        return $authUser->can('Replicate:MpProductionBatch');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MpProductionBatch');
    }

}