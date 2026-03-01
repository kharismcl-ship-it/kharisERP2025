<?php
namespace Modules\HR\Policies;
use App\Models\User;
use Modules\HR\Models\EmployeeGoal;
class EmployeeGoalPolicy {
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, EmployeeGoal $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, EmployeeGoal $model): bool { return true; }
    public function delete(User $user, EmployeeGoal $model): bool { return true; }
    public function restore(User $user, EmployeeGoal $model): bool { return true; }
    public function forceDelete(User $user, EmployeeGoal $model): bool { return true; }
}
