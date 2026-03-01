<?php
namespace Modules\HR\Policies;
use App\Models\User;
use Modules\HR\Models\DisciplinaryCase;
class DisciplinaryCasePolicy {
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, DisciplinaryCase $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, DisciplinaryCase $model): bool { return true; }
    public function delete(User $user, DisciplinaryCase $model): bool { return true; }
    public function restore(User $user, DisciplinaryCase $model): bool { return true; }
    public function forceDelete(User $user, DisciplinaryCase $model): bool { return true; }
}
