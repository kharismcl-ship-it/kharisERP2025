<?php

declare(strict_types=1);

namespace Modules\CommunicationCentre\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\CommunicationCentre\Models\CommProviderConfig;

class CommProviderConfigPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CommProviderConfig');
    }

    public function view(AuthUser $authUser, CommProviderConfig $commProviderConfig): bool
    {
        return $authUser->can('View:CommProviderConfig');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CommProviderConfig');
    }

    public function update(AuthUser $authUser, CommProviderConfig $commProviderConfig): bool
    {
        return $authUser->can('Update:CommProviderConfig');
    }

    public function delete(AuthUser $authUser, CommProviderConfig $commProviderConfig): bool
    {
        return $authUser->can('Delete:CommProviderConfig');
    }

    public function restore(AuthUser $authUser, CommProviderConfig $commProviderConfig): bool
    {
        return $authUser->can('Restore:CommProviderConfig');
    }

    public function forceDelete(AuthUser $authUser, CommProviderConfig $commProviderConfig): bool
    {
        return $authUser->can('ForceDelete:CommProviderConfig');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CommProviderConfig');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CommProviderConfig');
    }

    public function replicate(AuthUser $authUser, CommProviderConfig $commProviderConfig): bool
    {
        return $authUser->can('Replicate:CommProviderConfig');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CommProviderConfig');
    }
}
