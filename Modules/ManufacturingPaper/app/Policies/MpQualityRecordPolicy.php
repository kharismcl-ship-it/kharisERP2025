<?php

declare(strict_types=1);

namespace Modules\ManufacturingPaper\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ManufacturingPaper\Models\MpQualityRecord;
use Illuminate\Auth\Access\HandlesAuthorization;

class MpQualityRecordPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MpQualityRecord');
    }

    public function view(AuthUser $authUser, MpQualityRecord $mpQualityRecord): bool
    {
        return $authUser->can('View:MpQualityRecord');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MpQualityRecord');
    }

    public function update(AuthUser $authUser, MpQualityRecord $mpQualityRecord): bool
    {
        return $authUser->can('Update:MpQualityRecord');
    }

    public function delete(AuthUser $authUser, MpQualityRecord $mpQualityRecord): bool
    {
        return $authUser->can('Delete:MpQualityRecord');
    }

    public function restore(AuthUser $authUser, MpQualityRecord $mpQualityRecord): bool
    {
        return $authUser->can('Restore:MpQualityRecord');
    }

    public function forceDelete(AuthUser $authUser, MpQualityRecord $mpQualityRecord): bool
    {
        return $authUser->can('ForceDelete:MpQualityRecord');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MpQualityRecord');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MpQualityRecord');
    }

    public function replicate(AuthUser $authUser, MpQualityRecord $mpQualityRecord): bool
    {
        return $authUser->can('Replicate:MpQualityRecord');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MpQualityRecord');
    }

}