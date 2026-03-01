<?php

namespace Modules\ManufacturingWater\Policies;

use App\Models\User;
use Modules\ManufacturingWater\Models\MwWaterTestRecord;

class MwWaterTestRecordPolicy
{
    public function viewAny(User $user): bool   { return $user->can('view_any_mw::water::test::record'); }
    public function view(User $user, MwWaterTestRecord $model): bool { return $user->can('view_mw::water::test::record'); }
    public function create(User $user): bool    { return $user->can('create_mw::water::test::record'); }
    public function update(User $user, MwWaterTestRecord $model): bool { return $user->can('update_mw::water::test::record'); }
    public function delete(User $user, MwWaterTestRecord $model): bool { return $user->can('delete_mw::water::test::record'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_mw::water::test::record'); }
}
