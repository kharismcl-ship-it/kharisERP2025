<?php
namespace Modules\HR\Policies;
use App\Models\User;
use Modules\HR\Models\GrievanceCase;
class GrievanceCasePolicy {
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, GrievanceCase $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, GrievanceCase $model): bool { return true; }
    public function delete(User $user, GrievanceCase $model): bool { return true; }
    public function restore(User $user, GrievanceCase $model): bool { return true; }
    public function forceDelete(User $user, GrievanceCase $model): bool { return true; }
}
