<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\LivestockHealthRecord;
use Illuminate\Auth\Access\HandlesAuthorization;

class LivestockHealthRecordPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LivestockHealthRecord');
    }

    public function view(AuthUser $authUser, LivestockHealthRecord $livestockHealthRecord): bool
    {
        return $authUser->can('View:LivestockHealthRecord');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LivestockHealthRecord');
    }

    public function update(AuthUser $authUser, LivestockHealthRecord $livestockHealthRecord): bool
    {
        return $authUser->can('Update:LivestockHealthRecord');
    }

    public function delete(AuthUser $authUser, LivestockHealthRecord $livestockHealthRecord): bool
    {
        return $authUser->can('Delete:LivestockHealthRecord');
    }

    public function restore(AuthUser $authUser, LivestockHealthRecord $livestockHealthRecord): bool
    {
        return $authUser->can('Restore:LivestockHealthRecord');
    }

    public function forceDelete(AuthUser $authUser, LivestockHealthRecord $livestockHealthRecord): bool
    {
        return $authUser->can('ForceDelete:LivestockHealthRecord');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LivestockHealthRecord');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LivestockHealthRecord');
    }

    public function replicate(AuthUser $authUser, LivestockHealthRecord $livestockHealthRecord): bool
    {
        return $authUser->can('Replicate:LivestockHealthRecord');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LivestockHealthRecord');
    }

}