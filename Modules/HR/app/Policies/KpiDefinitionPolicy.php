<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\KpiDefinition;
use Illuminate\Auth\Access\HandlesAuthorization;

class KpiDefinitionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:KpiDefinition');
    }

    public function view(AuthUser $authUser, KpiDefinition $kpiDefinition): bool
    {
        return $authUser->can('View:KpiDefinition');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:KpiDefinition');
    }

    public function update(AuthUser $authUser, KpiDefinition $kpiDefinition): bool
    {
        return $authUser->can('Update:KpiDefinition');
    }

    public function delete(AuthUser $authUser, KpiDefinition $kpiDefinition): bool
    {
        return $authUser->can('Delete:KpiDefinition');
    }

    public function restore(AuthUser $authUser, KpiDefinition $kpiDefinition): bool
    {
        return $authUser->can('Restore:KpiDefinition');
    }

    public function forceDelete(AuthUser $authUser, KpiDefinition $kpiDefinition): bool
    {
        return $authUser->can('ForceDelete:KpiDefinition');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:KpiDefinition');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:KpiDefinition');
    }

    public function replicate(AuthUser $authUser, KpiDefinition $kpiDefinition): bool
    {
        return $authUser->can('Replicate:KpiDefinition');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:KpiDefinition');
    }

}