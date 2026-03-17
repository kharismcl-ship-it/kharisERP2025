<?php

declare(strict_types=1);

namespace Modules\ITSupport\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ITSupport\Models\ItTrainingSession;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItTrainingSessionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ItTrainingSession');
    }

    public function view(AuthUser $authUser, ItTrainingSession $itTrainingSession): bool
    {
        return $authUser->can('View:ItTrainingSession');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ItTrainingSession');
    }

    public function update(AuthUser $authUser, ItTrainingSession $itTrainingSession): bool
    {
        return $authUser->can('Update:ItTrainingSession');
    }

    public function delete(AuthUser $authUser, ItTrainingSession $itTrainingSession): bool
    {
        return $authUser->can('Delete:ItTrainingSession');
    }

    public function restore(AuthUser $authUser, ItTrainingSession $itTrainingSession): bool
    {
        return $authUser->can('Restore:ItTrainingSession');
    }

    public function forceDelete(AuthUser $authUser, ItTrainingSession $itTrainingSession): bool
    {
        return $authUser->can('ForceDelete:ItTrainingSession');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ItTrainingSession');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ItTrainingSession');
    }

    public function replicate(AuthUser $authUser, ItTrainingSession $itTrainingSession): bool
    {
        return $authUser->can('Replicate:ItTrainingSession');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ItTrainingSession');
    }

}