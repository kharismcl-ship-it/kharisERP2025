<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\FarmDailyReport;
use Illuminate\Auth\Access\HandlesAuthorization;

class FarmDailyReportPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FarmDailyReport');
    }

    public function view(AuthUser $authUser, FarmDailyReport $farmDailyReport): bool
    {
        return $authUser->can('View:FarmDailyReport');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FarmDailyReport');
    }

    public function update(AuthUser $authUser, FarmDailyReport $farmDailyReport): bool
    {
        return $authUser->can('Update:FarmDailyReport');
    }

    public function delete(AuthUser $authUser, FarmDailyReport $farmDailyReport): bool
    {
        return $authUser->can('Delete:FarmDailyReport');
    }

    public function restore(AuthUser $authUser, FarmDailyReport $farmDailyReport): bool
    {
        return $authUser->can('Restore:FarmDailyReport');
    }

    public function forceDelete(AuthUser $authUser, FarmDailyReport $farmDailyReport): bool
    {
        return $authUser->can('ForceDelete:FarmDailyReport');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FarmDailyReport');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FarmDailyReport');
    }

    public function replicate(AuthUser $authUser, FarmDailyReport $farmDailyReport): bool
    {
        return $authUser->can('Replicate:FarmDailyReport');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FarmDailyReport');
    }

}