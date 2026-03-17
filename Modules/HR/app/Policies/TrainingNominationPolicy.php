<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\TrainingNomination;

class TrainingNominationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TrainingNomination');
    }

    public function view(AuthUser $authUser, TrainingNomination $trainingNomination): bool
    {
        return $authUser->can('View:TrainingNomination');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TrainingNomination');
    }

    public function update(AuthUser $authUser, TrainingNomination $trainingNomination): bool
    {
        return $authUser->can('Update:TrainingNomination');
    }

    public function delete(AuthUser $authUser, TrainingNomination $trainingNomination): bool
    {
        return $authUser->can('Delete:TrainingNomination');
    }

    public function restore(AuthUser $authUser, TrainingNomination $trainingNomination): bool
    {
        return $authUser->can('Restore:TrainingNomination');
    }

    public function forceDelete(AuthUser $authUser, TrainingNomination $trainingNomination): bool
    {
        return $authUser->can('ForceDelete:TrainingNomination');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TrainingNomination');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TrainingNomination');
    }

    public function replicate(AuthUser $authUser, TrainingNomination $trainingNomination): bool
    {
        return $authUser->can('Replicate:TrainingNomination');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TrainingNomination');
    }
}
