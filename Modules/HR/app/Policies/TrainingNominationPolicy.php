<?php
namespace Modules\HR\Policies;
use App\Models\User;
use Modules\HR\Models\TrainingNomination;
class TrainingNominationPolicy {
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, TrainingNomination $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, TrainingNomination $model): bool { return true; }
    public function delete(User $user, TrainingNomination $model): bool { return true; }
    public function restore(User $user, TrainingNomination $model): bool { return true; }
    public function forceDelete(User $user, TrainingNomination $model): bool { return true; }
}
