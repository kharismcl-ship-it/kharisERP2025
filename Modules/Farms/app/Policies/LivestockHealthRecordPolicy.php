<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\LivestockHealthRecord;

class LivestockHealthRecordPolicy
{
    public function viewAny(User $user): bool   { return $user->can('view_any_livestock_health_record'); }
    public function view(User $user, LivestockHealthRecord $r): bool { return $user->can('view_livestock_health_record'); }
    public function create(User $user): bool    { return $user->can('create_livestock_health_record'); }
    public function update(User $user, LivestockHealthRecord $r): bool { return $user->can('update_livestock_health_record'); }
    public function delete(User $user, LivestockHealthRecord $r): bool { return $user->can('delete_livestock_health_record'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_livestock_health_record'); }
}