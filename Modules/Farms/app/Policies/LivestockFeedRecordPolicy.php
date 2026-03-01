<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\LivestockFeedRecord;

class LivestockFeedRecordPolicy
{
    public function viewAny(User $user): bool   { return $user->can('view_any_livestock_feed_record'); }
    public function view(User $user, LivestockFeedRecord $r): bool { return $user->can('view_livestock_feed_record'); }
    public function create(User $user): bool    { return $user->can('create_livestock_feed_record'); }
    public function update(User $user, LivestockFeedRecord $r): bool { return $user->can('update_livestock_feed_record'); }
    public function delete(User $user, LivestockFeedRecord $r): bool { return $user->can('delete_livestock_feed_record'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_livestock_feed_record'); }
}