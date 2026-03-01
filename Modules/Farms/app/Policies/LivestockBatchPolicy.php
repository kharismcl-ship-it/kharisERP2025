<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\LivestockBatch;

class LivestockBatchPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_livestock_batch'); }
    public function view(User $user, LivestockBatch $r): bool { return $user->can('view_livestock_batch'); }
    public function create(User $user): bool   { return $user->can('create_livestock_batch'); }
    public function update(User $user, LivestockBatch $r): bool { return $user->can('update_livestock_batch'); }
    public function delete(User $user, LivestockBatch $r): bool { return $user->can('delete_livestock_batch'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_livestock_batch'); }
}
