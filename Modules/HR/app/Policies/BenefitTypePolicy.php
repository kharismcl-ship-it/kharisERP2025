<?php
namespace Modules\HR\Policies;
use App\Models\User;
use Modules\HR\Models\BenefitType;
class BenefitTypePolicy {
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, BenefitType $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, BenefitType $model): bool { return true; }
    public function delete(User $user, BenefitType $model): bool { return true; }
    public function restore(User $user, BenefitType $model): bool { return true; }
    public function forceDelete(User $user, BenefitType $model): bool { return true; }
}
