<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\TrainingProgram;
use Illuminate\Auth\Access\HandlesAuthorization;

class TrainingProgramPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TrainingProgram');
    }

    public function view(AuthUser $authUser, TrainingProgram $trainingProgram): bool
    {
        return $authUser->can('View:TrainingProgram');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TrainingProgram');
    }

    public function update(AuthUser $authUser, TrainingProgram $trainingProgram): bool
    {
        return $authUser->can('Update:TrainingProgram');
    }

    public function delete(AuthUser $authUser, TrainingProgram $trainingProgram): bool
    {
        return $authUser->can('Delete:TrainingProgram');
    }

    public function restore(AuthUser $authUser, TrainingProgram $trainingProgram): bool
    {
        return $authUser->can('Restore:TrainingProgram');
    }

    public function forceDelete(AuthUser $authUser, TrainingProgram $trainingProgram): bool
    {
        return $authUser->can('ForceDelete:TrainingProgram');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TrainingProgram');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TrainingProgram');
    }

    public function replicate(AuthUser $authUser, TrainingProgram $trainingProgram): bool
    {
        return $authUser->can('Replicate:TrainingProgram');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TrainingProgram');
    }

}