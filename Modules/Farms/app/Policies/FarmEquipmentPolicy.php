<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\FarmEquipment;

class FarmEquipmentPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_farm_equipment'); }
    public function view(User $user, FarmEquipment $record): bool { return $user->can('view_farm_equipment'); }
    public function create(User $user): bool   { return $user->can('create_farm_equipment'); }
    public function update(User $user, FarmEquipment $record): bool { return $user->can('update_farm_equipment'); }
    public function delete(User $user, FarmEquipment $record): bool { return $user->can('delete_farm_equipment'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_farm_equipment'); }
}