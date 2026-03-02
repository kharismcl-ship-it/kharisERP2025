<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\PublicHoliday;
use Illuminate\Auth\Access\HandlesAuthorization;

class PublicHolidayPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PublicHoliday');
    }

    public function view(AuthUser $authUser, PublicHoliday $publicHoliday): bool
    {
        return $authUser->can('View:PublicHoliday');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PublicHoliday');
    }

    public function update(AuthUser $authUser, PublicHoliday $publicHoliday): bool
    {
        return $authUser->can('Update:PublicHoliday');
    }

    public function delete(AuthUser $authUser, PublicHoliday $publicHoliday): bool
    {
        return $authUser->can('Delete:PublicHoliday');
    }

    public function restore(AuthUser $authUser, PublicHoliday $publicHoliday): bool
    {
        return $authUser->can('Restore:PublicHoliday');
    }

    public function forceDelete(AuthUser $authUser, PublicHoliday $publicHoliday): bool
    {
        return $authUser->can('ForceDelete:PublicHoliday');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PublicHoliday');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PublicHoliday');
    }

    public function replicate(AuthUser $authUser, PublicHoliday $publicHoliday): bool
    {
        return $authUser->can('Replicate:PublicHoliday');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PublicHoliday');
    }

}