<?php

namespace Modules\ManufacturingPaper\Policies;

use App\Models\User;
use Modules\ManufacturingPaper\Models\MpPaperGrade;

class MpPaperGradePolicy
{
    public function viewAny(User $user): bool   { return $user->can('view_any_mp::paper::grade'); }
    public function view(User $user, MpPaperGrade $model): bool { return $user->can('view_mp::paper::grade'); }
    public function create(User $user): bool    { return $user->can('create_mp::paper::grade'); }
    public function update(User $user, MpPaperGrade $model): bool { return $user->can('update_mp::paper::grade'); }
    public function delete(User $user, MpPaperGrade $model): bool { return $user->can('delete_mp::paper::grade'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_mp::paper::grade'); }
}
