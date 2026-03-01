<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\HarvestRecord;

class HarvestRecordPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_harvest_record'); }
    public function view(User $user, HarvestRecord $r): bool { return $user->can('view_harvest_record'); }
    public function create(User $user): bool   { return $user->can('create_harvest_record'); }
    public function update(User $user, HarvestRecord $r): bool { return $user->can('update_harvest_record'); }
    public function delete(User $user, HarvestRecord $r): bool { return $user->can('delete_harvest_record'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_harvest_record'); }
}
