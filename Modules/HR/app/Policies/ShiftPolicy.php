<?php
namespace Modules\HR\Policies;
use App\Models\User;
use Modules\HR\Models\Shift;
class ShiftPolicy {
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Shift $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Shift $model): bool { return true; }
    public function delete(User $user, Shift $model): bool { return true; }
    public function restore(User $user, Shift $model): bool { return true; }
    public function forceDelete(User $user, Shift $model): bool { return true; }
}
