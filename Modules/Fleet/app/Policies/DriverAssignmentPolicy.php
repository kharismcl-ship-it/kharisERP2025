<?php

namespace Modules\Fleet\Policies;

use App\Models\User;
use Modules\Fleet\Models\DriverAssignment;

class DriverAssignmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_driver_assignment');
    }

    public function view(User $user, DriverAssignment $assignment): bool
    {
        return $user->can('view_driver_assignment');
    }

    public function create(User $user): bool
    {
        return $user->can('create_driver_assignment');
    }

    public function update(User $user, DriverAssignment $assignment): bool
    {
        return $user->can('update_driver_assignment');
    }

    public function delete(User $user, DriverAssignment $assignment): bool
    {
        return $user->can('delete_driver_assignment');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_driver_assignment');
    }
}
