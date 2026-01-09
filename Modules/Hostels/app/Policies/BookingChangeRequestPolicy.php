<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\BookingChangeRequest;

class BookingChangeRequestPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BookingChangeRequest');
    }

    public function view(AuthUser $authUser, BookingChangeRequest $bookingChangeRequest): bool
    {
        return $authUser->can('View:BookingChangeRequest');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BookingChangeRequest');
    }

    public function update(AuthUser $authUser, BookingChangeRequest $bookingChangeRequest): bool
    {
        return $authUser->can('Update:BookingChangeRequest');
    }

    public function delete(AuthUser $authUser, BookingChangeRequest $bookingChangeRequest): bool
    {
        return $authUser->can('Delete:BookingChangeRequest');
    }

    public function restore(AuthUser $authUser, BookingChangeRequest $bookingChangeRequest): bool
    {
        return $authUser->can('Restore:BookingChangeRequest');
    }

    public function forceDelete(AuthUser $authUser, BookingChangeRequest $bookingChangeRequest): bool
    {
        return $authUser->can('ForceDelete:BookingChangeRequest');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BookingChangeRequest');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BookingChangeRequest');
    }

    public function replicate(AuthUser $authUser, BookingChangeRequest $bookingChangeRequest): bool
    {
        return $authUser->can('Replicate:BookingChangeRequest');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BookingChangeRequest');
    }
}
