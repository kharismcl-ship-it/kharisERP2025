<?php
namespace Modules\HR\Policies;
use App\Models\User;
use Modules\HR\Models\AllowanceType;
class AllowanceTypePolicy {
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, AllowanceType $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, AllowanceType $model): bool { return true; }
    public function delete(User $user, AllowanceType $model): bool { return true; }
    public function restore(User $user, AllowanceType $model): bool { return true; }
    public function forceDelete(User $user, AllowanceType $model): bool { return true; }
}
