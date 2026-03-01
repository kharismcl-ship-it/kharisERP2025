<?php
namespace Modules\HR\Policies;
use App\Models\User;
use Modules\HR\Models\EmployeeLoan;
class EmployeeLoanPolicy {
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, EmployeeLoan $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, EmployeeLoan $model): bool { return true; }
    public function delete(User $user, EmployeeLoan $model): bool { return true; }
    public function restore(User $user, EmployeeLoan $model): bool { return true; }
    public function forceDelete(User $user, EmployeeLoan $model): bool { return true; }
}
