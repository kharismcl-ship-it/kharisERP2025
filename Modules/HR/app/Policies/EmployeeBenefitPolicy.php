<?php
namespace Modules\HR\Policies;
use App\Models\User;
use Modules\HR\Models\EmployeeBenefit;
class EmployeeBenefitPolicy {
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, EmployeeBenefit $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, EmployeeBenefit $model): bool { return true; }
    public function delete(User $user, EmployeeBenefit $model): bool { return true; }
    public function restore(User $user, EmployeeBenefit $model): bool { return true; }
    public function forceDelete(User $user, EmployeeBenefit $model): bool { return true; }
}
