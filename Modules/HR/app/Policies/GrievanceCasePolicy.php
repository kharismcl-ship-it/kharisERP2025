<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\GrievanceCase;
use Illuminate\Auth\Access\HandlesAuthorization;

class GrievanceCasePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:GrievanceCase');
    }

    public function view(AuthUser $authUser, GrievanceCase $grievanceCase): bool
    {
        return $authUser->can('View:GrievanceCase');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:GrievanceCase');
    }

    public function update(AuthUser $authUser, GrievanceCase $grievanceCase): bool
    {
        return $authUser->can('Update:GrievanceCase');
    }

    public function delete(AuthUser $authUser, GrievanceCase $grievanceCase): bool
    {
        return $authUser->can('Delete:GrievanceCase');
    }

    public function restore(AuthUser $authUser, GrievanceCase $grievanceCase): bool
    {
        return $authUser->can('Restore:GrievanceCase');
    }

    public function forceDelete(AuthUser $authUser, GrievanceCase $grievanceCase): bool
    {
        return $authUser->can('ForceDelete:GrievanceCase');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:GrievanceCase');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:GrievanceCase');
    }

    public function replicate(AuthUser $authUser, GrievanceCase $grievanceCase): bool
    {
        return $authUser->can('Replicate:GrievanceCase');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:GrievanceCase');
    }

}