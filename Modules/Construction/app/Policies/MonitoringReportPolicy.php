<?php

declare(strict_types=1);

namespace Modules\Construction\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Construction\Models\MonitoringReport;
use Illuminate\Auth\Access\HandlesAuthorization;

class MonitoringReportPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MonitoringReport');
    }

    public function view(AuthUser $authUser, MonitoringReport $monitoringReport): bool
    {
        return $authUser->can('View:MonitoringReport');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MonitoringReport');
    }

    public function update(AuthUser $authUser, MonitoringReport $monitoringReport): bool
    {
        return $authUser->can('Update:MonitoringReport');
    }

    public function delete(AuthUser $authUser, MonitoringReport $monitoringReport): bool
    {
        return $authUser->can('Delete:MonitoringReport');
    }

    public function restore(AuthUser $authUser, MonitoringReport $monitoringReport): bool
    {
        return $authUser->can('Restore:MonitoringReport');
    }

    public function forceDelete(AuthUser $authUser, MonitoringReport $monitoringReport): bool
    {
        return $authUser->can('ForceDelete:MonitoringReport');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MonitoringReport');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MonitoringReport');
    }

    public function replicate(AuthUser $authUser, MonitoringReport $monitoringReport): bool
    {
        return $authUser->can('Replicate:MonitoringReport');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MonitoringReport');
    }

}