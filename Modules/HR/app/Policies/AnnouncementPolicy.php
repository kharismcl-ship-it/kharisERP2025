<?php
namespace Modules\HR\Policies;
use App\Models\User;
use Modules\HR\Models\Announcement;
class AnnouncementPolicy {
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Announcement $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Announcement $model): bool { return true; }
    public function delete(User $user, Announcement $model): bool { return true; }
    public function restore(User $user, Announcement $model): bool { return true; }
    public function forceDelete(User $user, Announcement $model): bool { return true; }
}
