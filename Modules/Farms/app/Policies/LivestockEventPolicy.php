<?php

namespace Modules\Farms\Policies;

use App\Models\User;
use Modules\Farms\Models\LivestockEvent;

class LivestockEventPolicy
{
    public function viewAny(User $user): bool  { return $user->can('view_any_livestock_event'); }
    public function view(User $user, LivestockEvent $record): bool { return $user->can('view_livestock_event'); }
    public function create(User $user): bool   { return $user->can('create_livestock_event'); }
    public function update(User $user, LivestockEvent $record): bool { return $user->can('update_livestock_event'); }
    public function delete(User $user, LivestockEvent $record): bool { return $user->can('delete_livestock_event'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_livestock_event'); }
}