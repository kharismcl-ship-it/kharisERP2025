<?php
namespace Modules\HR\Policies;
use App\Models\User;
use Modules\HR\Models\JobVacancy;
class JobVacancyPolicy {
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, JobVacancy $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, JobVacancy $model): bool { return true; }
    public function delete(User $user, JobVacancy $model): bool { return true; }
    public function restore(User $user, JobVacancy $model): bool { return true; }
    public function forceDelete(User $user, JobVacancy $model): bool { return true; }
}
