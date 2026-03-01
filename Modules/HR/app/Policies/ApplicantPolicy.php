<?php
namespace Modules\HR\Policies;
use App\Models\User;
use Modules\HR\Models\Applicant;
class ApplicantPolicy {
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Applicant $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Applicant $model): bool { return true; }
    public function delete(User $user, Applicant $model): bool { return true; }
    public function restore(User $user, Applicant $model): bool { return true; }
    public function forceDelete(User $user, Applicant $model): bool { return true; }
}
