<?php

namespace Modules\ManufacturingPaper\Policies;

use App\Models\User;
use Modules\ManufacturingPaper\Models\MpEquipmentLog;

class MpEquipmentLogPolicy
{
    public function viewAny(User $user): bool   { return $user->can('view_any_mp::equipment::log'); }
    public function view(User $user, MpEquipmentLog $model): bool { return $user->can('view_mp::equipment::log'); }
    public function create(User $user): bool    { return $user->can('create_mp::equipment::log'); }
    public function update(User $user, MpEquipmentLog $model): bool { return $user->can('update_mp::equipment::log'); }
    public function delete(User $user, MpEquipmentLog $model): bool { return $user->can('delete_mp::equipment::log'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_mp::equipment::log'); }
}
