<?php

declare(strict_types=1);

namespace Modules\CommunicationCentre\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\CommunicationCentre\Models\CommMessage;

class CommMessagePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CommMessage');
    }

    public function view(AuthUser $authUser, CommMessage $commMessage): bool
    {
        return $authUser->can('View:CommMessage');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CommMessage');
    }

    public function update(AuthUser $authUser, CommMessage $commMessage): bool
    {
        return $authUser->can('Update:CommMessage');
    }

    public function delete(AuthUser $authUser, CommMessage $commMessage): bool
    {
        return $authUser->can('Delete:CommMessage');
    }

    public function restore(AuthUser $authUser, CommMessage $commMessage): bool
    {
        return $authUser->can('Restore:CommMessage');
    }

    public function forceDelete(AuthUser $authUser, CommMessage $commMessage): bool
    {
        return $authUser->can('ForceDelete:CommMessage');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CommMessage');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CommMessage');
    }

    public function replicate(AuthUser $authUser, CommMessage $commMessage): bool
    {
        return $authUser->can('Replicate:CommMessage');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CommMessage');
    }
}
