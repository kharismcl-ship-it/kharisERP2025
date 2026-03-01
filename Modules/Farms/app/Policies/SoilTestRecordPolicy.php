<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\SoilTestRecord;

class SoilTestRecordPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_soil_test_record'); }
    public function view(User $user, SoilTestRecord $record): bool { return $user->can('view_soil_test_record'); }
    public function create(User $user): bool   { return $user->can('create_soil_test_record'); }
    public function update(User $user, SoilTestRecord $record): bool { return $user->can('update_soil_test_record'); }
    public function delete(User $user, SoilTestRecord $record): bool { return $user->can('delete_soil_test_record'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_soil_test_record'); }
}