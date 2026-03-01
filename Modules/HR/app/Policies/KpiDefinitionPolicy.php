<?php
namespace Modules\HR\Policies;
use App\Models\User;
use Modules\HR\Models\KpiDefinition;
class KpiDefinitionPolicy {
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, KpiDefinition $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, KpiDefinition $model): bool { return true; }
    public function delete(User $user, KpiDefinition $model): bool { return true; }
    public function restore(User $user, KpiDefinition $model): bool { return true; }
    public function forceDelete(User $user, KpiDefinition $model): bool { return true; }
}
