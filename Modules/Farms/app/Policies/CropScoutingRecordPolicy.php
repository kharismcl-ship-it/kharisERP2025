<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\CropScoutingRecord;

class CropScoutingRecordPolicy
{
    public function viewAny(User $user): bool   { return $user->can('view_any_crop_scouting_record'); }
    public function view(User $user, CropScoutingRecord $r): bool { return $user->can('view_crop_scouting_record'); }
    public function create(User $user): bool    { return $user->can('create_crop_scouting_record'); }
    public function update(User $user, CropScoutingRecord $r): bool { return $user->can('update_crop_scouting_record'); }
    public function delete(User $user, CropScoutingRecord $r): bool { return $user->can('delete_crop_scouting_record'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_crop_scouting_record'); }
}