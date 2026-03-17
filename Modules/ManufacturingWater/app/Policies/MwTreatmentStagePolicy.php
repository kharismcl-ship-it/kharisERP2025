<?php

declare(strict_types=1);

namespace Modules\ManufacturingWater\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ManufacturingWater\Models\MwTreatmentStage;
use Illuminate\Auth\Access\HandlesAuthorization;

class MwTreatmentStagePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MwTreatmentStage');
    }

    public function view(AuthUser $authUser, MwTreatmentStage $mwTreatmentStage): bool
    {
        return $authUser->can('View:MwTreatmentStage');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MwTreatmentStage');
    }

    public function update(AuthUser $authUser, MwTreatmentStage $mwTreatmentStage): bool
    {
        return $authUser->can('Update:MwTreatmentStage');
    }

    public function delete(AuthUser $authUser, MwTreatmentStage $mwTreatmentStage): bool
    {
        return $authUser->can('Delete:MwTreatmentStage');
    }

    public function restore(AuthUser $authUser, MwTreatmentStage $mwTreatmentStage): bool
    {
        return $authUser->can('Restore:MwTreatmentStage');
    }

    public function forceDelete(AuthUser $authUser, MwTreatmentStage $mwTreatmentStage): bool
    {
        return $authUser->can('ForceDelete:MwTreatmentStage');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MwTreatmentStage');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MwTreatmentStage');
    }

    public function replicate(AuthUser $authUser, MwTreatmentStage $mwTreatmentStage): bool
    {
        return $authUser->can('Replicate:MwTreatmentStage');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MwTreatmentStage');
    }

}