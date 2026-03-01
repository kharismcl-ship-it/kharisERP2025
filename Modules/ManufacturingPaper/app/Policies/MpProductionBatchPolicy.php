<?php

namespace Modules\ManufacturingPaper\Policies;

use App\Models\User;
use Modules\ManufacturingPaper\Models\MpProductionBatch;

class MpProductionBatchPolicy
{
    public function viewAny(User $user): bool   { return $user->can('view_any_mp::production::batch'); }
    public function view(User $user, MpProductionBatch $model): bool { return $user->can('view_mp::production::batch'); }
    public function create(User $user): bool    { return $user->can('create_mp::production::batch'); }
    public function update(User $user, MpProductionBatch $model): bool { return $user->can('update_mp::production::batch'); }
    public function delete(User $user, MpProductionBatch $model): bool { return $user->can('delete_mp::production::batch'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_mp::production::batch'); }
}
