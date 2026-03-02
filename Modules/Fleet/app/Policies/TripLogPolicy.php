<?php

declare(strict_types=1);

namespace Modules\Fleet\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Fleet\Models\TripLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class TripLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TripLog');
    }

    public function view(AuthUser $authUser, TripLog $tripLog): bool
    {
        return $authUser->can('View:TripLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TripLog');
    }

    public function update(AuthUser $authUser, TripLog $tripLog): bool
    {
        return $authUser->can('Update:TripLog');
    }

    public function delete(AuthUser $authUser, TripLog $tripLog): bool
    {
        return $authUser->can('Delete:TripLog');
    }

    public function restore(AuthUser $authUser, TripLog $tripLog): bool
    {
        return $authUser->can('Restore:TripLog');
    }

    public function forceDelete(AuthUser $authUser, TripLog $tripLog): bool
    {
        return $authUser->can('ForceDelete:TripLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TripLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TripLog');
    }

    public function replicate(AuthUser $authUser, TripLog $tripLog): bool
    {
        return $authUser->can('Replicate:TripLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TripLog');
    }

}