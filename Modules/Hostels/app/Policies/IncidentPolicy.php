<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\Incident;
use Illuminate\Auth\Access\HandlesAuthorization;

class IncidentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Incident');
    }

    public function view(AuthUser $authUser, Incident $incident): bool
    {
        return $authUser->can('View:Incident');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Incident');
    }

    public function update(AuthUser $authUser, Incident $incident): bool
    {
        return $authUser->can('Update:Incident');
    }

    public function delete(AuthUser $authUser, Incident $incident): bool
    {
        return $authUser->can('Delete:Incident');
    }

    public function restore(AuthUser $authUser, Incident $incident): bool
    {
        return $authUser->can('Restore:Incident');
    }

    public function forceDelete(AuthUser $authUser, Incident $incident): bool
    {
        return $authUser->can('ForceDelete:Incident');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Incident');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Incident');
    }

    public function replicate(AuthUser $authUser, Incident $incident): bool
    {
        return $authUser->can('Replicate:Incident');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Incident');
    }

}