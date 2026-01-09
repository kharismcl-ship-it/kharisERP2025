<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\PerformanceCycle;

class PerformanceCyclePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PerformanceCycle');
    }

    public function view(AuthUser $authUser, PerformanceCycle $performanceCycle): bool
    {
        return $authUser->can('View:PerformanceCycle');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PerformanceCycle');
    }

    public function update(AuthUser $authUser, PerformanceCycle $performanceCycle): bool
    {
        return $authUser->can('Update:PerformanceCycle');
    }

    public function delete(AuthUser $authUser, PerformanceCycle $performanceCycle): bool
    {
        return $authUser->can('Delete:PerformanceCycle');
    }

    public function restore(AuthUser $authUser, PerformanceCycle $performanceCycle): bool
    {
        return $authUser->can('Restore:PerformanceCycle');
    }

    public function forceDelete(AuthUser $authUser, PerformanceCycle $performanceCycle): bool
    {
        return $authUser->can('ForceDelete:PerformanceCycle');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PerformanceCycle');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PerformanceCycle');
    }

    public function replicate(AuthUser $authUser, PerformanceCycle $performanceCycle): bool
    {
        return $authUser->can('Replicate:PerformanceCycle');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PerformanceCycle');
    }
}
