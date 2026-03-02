<?php

declare(strict_types=1);

namespace Modules\ManufacturingWater\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ManufacturingWater\Models\MwDistributionRecord;
use Illuminate\Auth\Access\HandlesAuthorization;

class MwDistributionRecordPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MwDistributionRecord');
    }

    public function view(AuthUser $authUser, MwDistributionRecord $mwDistributionRecord): bool
    {
        return $authUser->can('View:MwDistributionRecord');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MwDistributionRecord');
    }

    public function update(AuthUser $authUser, MwDistributionRecord $mwDistributionRecord): bool
    {
        return $authUser->can('Update:MwDistributionRecord');
    }

    public function delete(AuthUser $authUser, MwDistributionRecord $mwDistributionRecord): bool
    {
        return $authUser->can('Delete:MwDistributionRecord');
    }

    public function restore(AuthUser $authUser, MwDistributionRecord $mwDistributionRecord): bool
    {
        return $authUser->can('Restore:MwDistributionRecord');
    }

    public function forceDelete(AuthUser $authUser, MwDistributionRecord $mwDistributionRecord): bool
    {
        return $authUser->can('ForceDelete:MwDistributionRecord');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MwDistributionRecord');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MwDistributionRecord');
    }

    public function replicate(AuthUser $authUser, MwDistributionRecord $mwDistributionRecord): bool
    {
        return $authUser->can('Replicate:MwDistributionRecord');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MwDistributionRecord');
    }

}