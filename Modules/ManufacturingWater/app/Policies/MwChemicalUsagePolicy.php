<?php

declare(strict_types=1);

namespace Modules\ManufacturingWater\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ManufacturingWater\Models\MwChemicalUsage;
use Illuminate\Auth\Access\HandlesAuthorization;

class MwChemicalUsagePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MwChemicalUsage');
    }

    public function view(AuthUser $authUser, MwChemicalUsage $mwChemicalUsage): bool
    {
        return $authUser->can('View:MwChemicalUsage');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MwChemicalUsage');
    }

    public function update(AuthUser $authUser, MwChemicalUsage $mwChemicalUsage): bool
    {
        return $authUser->can('Update:MwChemicalUsage');
    }

    public function delete(AuthUser $authUser, MwChemicalUsage $mwChemicalUsage): bool
    {
        return $authUser->can('Delete:MwChemicalUsage');
    }

    public function restore(AuthUser $authUser, MwChemicalUsage $mwChemicalUsage): bool
    {
        return $authUser->can('Restore:MwChemicalUsage');
    }

    public function forceDelete(AuthUser $authUser, MwChemicalUsage $mwChemicalUsage): bool
    {
        return $authUser->can('ForceDelete:MwChemicalUsage');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MwChemicalUsage');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MwChemicalUsage');
    }

    public function replicate(AuthUser $authUser, MwChemicalUsage $mwChemicalUsage): bool
    {
        return $authUser->can('Replicate:MwChemicalUsage');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MwChemicalUsage');
    }

}