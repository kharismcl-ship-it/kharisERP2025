<?php

namespace Modules\ManufacturingPaper\Policies;

use App\Models\User;
use Modules\ManufacturingPaper\Models\MpQualityRecord;

class MpQualityRecordPolicy
{
    public function viewAny(User $user): bool   { return $user->can('view_any_mp::quality::record'); }
    public function view(User $user, MpQualityRecord $model): bool { return $user->can('view_mp::quality::record'); }
    public function create(User $user): bool    { return $user->can('create_mp::quality::record'); }
    public function update(User $user, MpQualityRecord $model): bool { return $user->can('update_mp::quality::record'); }
    public function delete(User $user, MpQualityRecord $model): bool { return $user->can('delete_mp::quality::record'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_mp::quality::record'); }
}
