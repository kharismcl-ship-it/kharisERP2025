<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\BookingCancellationPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingCancellationPolicyPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BookingCancellationPolicy');
    }

    public function view(AuthUser $authUser, BookingCancellationPolicy $bookingCancellationPolicy): bool
    {
        return $authUser->can('View:BookingCancellationPolicy');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BookingCancellationPolicy');
    }

    public function update(AuthUser $authUser, BookingCancellationPolicy $bookingCancellationPolicy): bool
    {
        return $authUser->can('Update:BookingCancellationPolicy');
    }

    public function delete(AuthUser $authUser, BookingCancellationPolicy $bookingCancellationPolicy): bool
    {
        return $authUser->can('Delete:BookingCancellationPolicy');
    }

    public function restore(AuthUser $authUser, BookingCancellationPolicy $bookingCancellationPolicy): bool
    {
        return $authUser->can('Restore:BookingCancellationPolicy');
    }

    public function forceDelete(AuthUser $authUser, BookingCancellationPolicy $bookingCancellationPolicy): bool
    {
        return $authUser->can('ForceDelete:BookingCancellationPolicy');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BookingCancellationPolicy');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BookingCancellationPolicy');
    }

    public function replicate(AuthUser $authUser, BookingCancellationPolicy $bookingCancellationPolicy): bool
    {
        return $authUser->can('Replicate:BookingCancellationPolicy');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BookingCancellationPolicy');
    }

}