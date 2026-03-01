<?php
namespace Modules\HR\Policies;
use App\Models\User;
use Modules\HR\Models\Certification;
class CertificationPolicy {
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Certification $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Certification $model): bool { return true; }
    public function delete(User $user, Certification $model): bool { return true; }
    public function restore(User $user, Certification $model): bool { return true; }
    public function forceDelete(User $user, Certification $model): bool { return true; }
}
