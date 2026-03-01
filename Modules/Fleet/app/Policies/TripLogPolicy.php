<?php

namespace Modules\Fleet\Policies;

use App\Models\User;
use Modules\Fleet\Models\TripLog;

class TripLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_trip_log');
    }

    public function view(User $user, TripLog $tripLog): bool
    {
        return $user->can('view_trip_log');
    }

    public function create(User $user): bool
    {
        return $user->can('create_trip_log');
    }

    public function update(User $user, TripLog $tripLog): bool
    {
        return $user->can('update_trip_log');
    }

    public function delete(User $user, TripLog $tripLog): bool
    {
        return $user->can('delete_trip_log');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_trip_log');
    }
}
