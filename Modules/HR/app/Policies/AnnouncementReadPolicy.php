<?php
namespace Modules\HR\Policies;
use App\Models\User;
use Modules\HR\Models\AnnouncementRead;
class AnnouncementReadPolicy {
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, AnnouncementRead $model): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, AnnouncementRead $model): bool { return true; }
    public function delete(User $user, AnnouncementRead $model): bool { return true; }
    public function restore(User $user, AnnouncementRead $model): bool { return true; }
    public function forceDelete(User $user, AnnouncementRead $model): bool { return true; }
}
