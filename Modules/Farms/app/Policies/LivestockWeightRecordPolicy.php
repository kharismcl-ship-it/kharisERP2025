<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\LivestockWeightRecord;

class LivestockWeightRecordPolicy
{
    public function viewAny(User $user): bool   { return $user->can('view_any_livestock_weight_record'); }
    public function view(User $user, LivestockWeightRecord $r): bool { return $user->can('view_livestock_weight_record'); }
    public function create(User $user): bool    { return $user->can('create_livestock_weight_record'); }
    public function update(User $user, LivestockWeightRecord $r): bool { return $user->can('update_livestock_weight_record'); }
    public function delete(User $user, LivestockWeightRecord $r): bool { return $user->can('delete_livestock_weight_record'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_livestock_weight_record'); }
}