<?php

declare(strict_types=1);

namespace Modules\Construction\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Construction\Models\SiteMonitor;
use Illuminate\Auth\Access\HandlesAuthorization;

class SiteMonitorPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SiteMonitor');
    }

    public function view(AuthUser $authUser, SiteMonitor $siteMonitor): bool
    {
        return $authUser->can('View:SiteMonitor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SiteMonitor');
    }

    public function update(AuthUser $authUser, SiteMonitor $siteMonitor): bool
    {
        return $authUser->can('Update:SiteMonitor');
    }

    public function delete(AuthUser $authUser, SiteMonitor $siteMonitor): bool
    {
        return $authUser->can('Delete:SiteMonitor');
    }

    public function restore(AuthUser $authUser, SiteMonitor $siteMonitor): bool
    {
        return $authUser->can('Restore:SiteMonitor');
    }

    public function forceDelete(AuthUser $authUser, SiteMonitor $siteMonitor): bool
    {
        return $authUser->can('ForceDelete:SiteMonitor');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SiteMonitor');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SiteMonitor');
    }

    public function replicate(AuthUser $authUser, SiteMonitor $siteMonitor): bool
    {
        return $authUser->can('Replicate:SiteMonitor');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SiteMonitor');
    }

}