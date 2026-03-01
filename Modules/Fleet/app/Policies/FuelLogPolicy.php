<?php

namespace Modules\Fleet\Policies;

use App\Models\User;
use Modules\Fleet\Models\FuelLog;

class FuelLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_fuel_log');
    }

    public function view(User $user, FuelLog $fuelLog): bool
    {
        return $user->can('view_fuel_log');
    }

    public function create(User $user): bool
    {
        return $user->can('create_fuel_log');
    }

    public function update(User $user, FuelLog $fuelLog): bool
    {
        return $user->can('update_fuel_log');
    }

    public function delete(User $user, FuelLog $fuelLog): bool
    {
        return $user->can('delete_fuel_log');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_fuel_log');
    }
}
