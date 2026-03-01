<?php

namespace Modules\Fleet\Policies;

use App\Models\User;
use Modules\Fleet\Models\Vehicle;

class VehiclePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_vehicle');
    }

    public function view(User $user, Vehicle $vehicle): bool
    {
        return $user->can('view_vehicle');
    }

    public function create(User $user): bool
    {
        return $user->can('create_vehicle');
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->can('update_vehicle');
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $user->can('delete_vehicle');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_vehicle');
    }
}
