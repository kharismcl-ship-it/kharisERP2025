<?php

declare(strict_types=1);

namespace Modules\ClientService\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ClientService\Models\CsVisitorProfile;
use Illuminate\Auth\Access\HandlesAuthorization;

class CsVisitorProfilePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CsVisitorProfile');
    }

    public function view(AuthUser $authUser, CsVisitorProfile $csVisitorProfile): bool
    {
        return $authUser->can('View:CsVisitorProfile');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CsVisitorProfile');
    }

    public function update(AuthUser $authUser, CsVisitorProfile $csVisitorProfile): bool
    {
        return $authUser->can('Update:CsVisitorProfile');
    }

    public function delete(AuthUser $authUser, CsVisitorProfile $csVisitorProfile): bool
    {
        return $authUser->can('Delete:CsVisitorProfile');
    }

    public function restore(AuthUser $authUser, CsVisitorProfile $csVisitorProfile): bool
    {
        return $authUser->can('Restore:CsVisitorProfile');
    }

    public function forceDelete(AuthUser $authUser, CsVisitorProfile $csVisitorProfile): bool
    {
        return $authUser->can('ForceDelete:CsVisitorProfile');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CsVisitorProfile');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CsVisitorProfile');
    }

    public function replicate(AuthUser $authUser, CsVisitorProfile $csVisitorProfile): bool
    {
        return $authUser->can('Replicate:CsVisitorProfile');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CsVisitorProfile');
    }

}