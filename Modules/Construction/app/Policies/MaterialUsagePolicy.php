<?php

declare(strict_types=1);

namespace Modules\Construction\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Construction\Models\MaterialUsage;
use Illuminate\Auth\Access\HandlesAuthorization;

class MaterialUsagePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MaterialUsage');
    }

    public function view(AuthUser $authUser, MaterialUsage $materialUsage): bool
    {
        return $authUser->can('View:MaterialUsage');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MaterialUsage');
    }

    public function update(AuthUser $authUser, MaterialUsage $materialUsage): bool
    {
        return $authUser->can('Update:MaterialUsage');
    }

    public function delete(AuthUser $authUser, MaterialUsage $materialUsage): bool
    {
        return $authUser->can('Delete:MaterialUsage');
    }

    public function restore(AuthUser $authUser, MaterialUsage $materialUsage): bool
    {
        return $authUser->can('Restore:MaterialUsage');
    }

    public function forceDelete(AuthUser $authUser, MaterialUsage $materialUsage): bool
    {
        return $authUser->can('ForceDelete:MaterialUsage');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MaterialUsage');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MaterialUsage');
    }

    public function replicate(AuthUser $authUser, MaterialUsage $materialUsage): bool
    {
        return $authUser->can('Replicate:MaterialUsage');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MaterialUsage');
    }

}