<?php
namespace Modules\HR\Policies;
use App\Models\User;
use Modules\HR\Models\LoanRepayment;
class LoanRepaymentPolicy {
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, LoanRepayment $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, LoanRepayment $model): bool { return true; }
    public function delete(User $user, LoanRepayment $model): bool { return true; }
    public function restore(User $user, LoanRepayment $model): bool { return true; }
    public function forceDelete(User $user, LoanRepayment $model): bool { return true; }
}
