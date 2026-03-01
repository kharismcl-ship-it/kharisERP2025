<?php

namespace Modules\ManufacturingWater\Policies;

use App\Models\User;
use Modules\ManufacturingWater\Models\MwDistributionRecord;

class MwDistributionRecordPolicy
{
    public function viewAny(User $user): bool   { return $user->can('view_any_mw::distribution::record'); }
    public function view(User $user, MwDistributionRecord $model): bool { return $user->can('view_mw::distribution::record'); }
    public function create(User $user): bool    { return $user->can('create_mw::distribution::record'); }
    public function update(User $user, MwDistributionRecord $model): bool { return $user->can('update_mw::distribution::record'); }
    public function delete(User $user, MwDistributionRecord $model): bool { return $user->can('delete_mw::distribution::record'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_mw::distribution::record'); }
}