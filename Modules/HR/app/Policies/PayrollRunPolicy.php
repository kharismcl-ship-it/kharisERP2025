<?php
namespace Modules\HR\Policies;
use App\Models\User;
use Modules\HR\Models\PayrollRun;
class PayrollRunPolicy {
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, PayrollRun $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, PayrollRun $model): bool { return true; }
    public function delete(User $user, PayrollRun $model): bool { return true; }
    public function restore(User $user, PayrollRun $model): bool { return true; }
    public function forceDelete(User $user, PayrollRun $model): bool { return true; }
}
