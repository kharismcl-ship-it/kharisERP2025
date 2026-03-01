<?php
namespace Modules\HR\Policies;
use App\Models\User;
use Modules\HR\Models\DeductionType;
class DeductionTypePolicy {
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, DeductionType $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, DeductionType $model): bool { return true; }
    public function delete(User $user, DeductionType $model): bool { return true; }
    public function restore(User $user, DeductionType $model): bool { return true; }
    public function forceDelete(User $user, DeductionType $model): bool { return true; }
}
