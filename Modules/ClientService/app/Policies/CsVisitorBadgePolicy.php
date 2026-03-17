<?php

declare(strict_types=1);

namespace Modules\ClientService\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ClientService\Models\CsVisitorBadge;
use Illuminate\Auth\Access\HandlesAuthorization;

class CsVisitorBadgePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CsVisitorBadge');
    }

    public function view(AuthUser $authUser, CsVisitorBadge $csVisitorBadge): bool
    {
        return $authUser->can('View:CsVisitorBadge');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CsVisitorBadge');
    }

    public function update(AuthUser $authUser, CsVisitorBadge $csVisitorBadge): bool
    {
        return $authUser->can('Update:CsVisitorBadge');
    }

    public function delete(AuthUser $authUser, CsVisitorBadge $csVisitorBadge): bool
    {
        return $authUser->can('Delete:CsVisitorBadge');
    }

    public function restore(AuthUser $authUser, CsVisitorBadge $csVisitorBadge): bool
    {
        return $authUser->can('Restore:CsVisitorBadge');
    }

    public function forceDelete(AuthUser $authUser, CsVisitorBadge $csVisitorBadge): bool
    {
        return $authUser->can('ForceDelete:CsVisitorBadge');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CsVisitorBadge');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CsVisitorBadge');
    }

    public function replicate(AuthUser $authUser, CsVisitorBadge $csVisitorBadge): bool
    {
        return $authUser->can('Replicate:CsVisitorBadge');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CsVisitorBadge');
    }

}