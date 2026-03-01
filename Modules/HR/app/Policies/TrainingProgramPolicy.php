<?php
namespace Modules\HR\Policies;
use App\Models\User;
use Modules\HR\Models\TrainingProgram;
class TrainingProgramPolicy {
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, TrainingProgram $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, TrainingProgram $model): bool { return true; }
    public function delete(User $user, TrainingProgram $model): bool { return true; }
    public function restore(User $user, TrainingProgram $model): bool { return true; }
    public function forceDelete(User $user, TrainingProgram $model): bool { return true; }
}
