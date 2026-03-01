<?php

namespace Modules\Fleet\Policies;

use App\Models\User;
use Modules\Fleet\Models\MaintenanceRecord;

class MaintenanceRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_maintenance_record');
    }

    public function view(User $user, MaintenanceRecord $record): bool
    {
        return $user->can('view_maintenance_record');
    }

    public function create(User $user): bool
    {
        return $user->can('create_maintenance_record');
    }

    public function update(User $user, MaintenanceRecord $record): bool
    {
        return $user->can('update_maintenance_record');
    }

    public function delete(User $user, MaintenanceRecord $record): bool
    {
        return $user->can('delete_maintenance_record');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_maintenance_record');
    }
}
