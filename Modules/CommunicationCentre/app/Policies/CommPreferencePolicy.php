<?php

declare(strict_types=1);

namespace Modules\CommunicationCentre\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\CommunicationCentre\Models\CommPreference;

class CommPreferencePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CommPreference');
    }

    public function view(AuthUser $authUser, CommPreference $commPreference): bool
    {
        return $authUser->can('View:CommPreference');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CommPreference');
    }

    public function update(AuthUser $authUser, CommPreference $commPreference): bool
    {
        return $authUser->can('Update:CommPreference');
    }

    public function delete(AuthUser $authUser, CommPreference $commPreference): bool
    {
        return $authUser->can('Delete:CommPreference');
    }

    public function restore(AuthUser $authUser, CommPreference $commPreference): bool
    {
        return $authUser->can('Restore:CommPreference');
    }

    public function forceDelete(AuthUser $authUser, CommPreference $commPreference): bool
    {
        return $authUser->can('ForceDelete:CommPreference');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CommPreference');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CommPreference');
    }

    public function replicate(AuthUser $authUser, CommPreference $commPreference): bool
    {
        return $authUser->can('Replicate:CommPreference');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CommPreference');
    }
}
