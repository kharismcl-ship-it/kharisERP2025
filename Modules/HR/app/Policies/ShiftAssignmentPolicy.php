<?php
namespace Modules\HR\Policies;
use App\Models\User;
use Modules\HR\Models\ShiftAssignment;
class ShiftAssignmentPolicy {
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, ShiftAssignment $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, ShiftAssignment $model): bool { return true; }
    public function delete(User $user, ShiftAssignment $model): bool { return true; }
    public function restore(User $user, ShiftAssignment $model): bool { return true; }
    public function forceDelete(User $user, ShiftAssignment $model): bool { return true; }
}
