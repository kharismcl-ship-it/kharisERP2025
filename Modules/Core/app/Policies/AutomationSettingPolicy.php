<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\AutomationSetting;
use Illuminate\Auth\Access\HandlesAuthorization;

class AutomationSettingPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AutomationSetting');
    }

    public function view(AuthUser $authUser, AutomationSetting $automationSetting): bool
    {
        return $authUser->can('View:AutomationSetting');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AutomationSetting');
    }

    public function update(AuthUser $authUser, AutomationSetting $automationSetting): bool
    {
        return $authUser->can('Update:AutomationSetting');
    }

    public function delete(AuthUser $authUser, AutomationSetting $automationSetting): bool
    {
        return $authUser->can('Delete:AutomationSetting');
    }

    public function restore(AuthUser $authUser, AutomationSetting $automationSetting): bool
    {
        return $authUser->can('Restore:AutomationSetting');
    }

    public function forceDelete(AuthUser $authUser, AutomationSetting $automationSetting): bool
    {
        return $authUser->can('ForceDelete:AutomationSetting');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AutomationSetting');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AutomationSetting');
    }

    public function replicate(AuthUser $authUser, AutomationSetting $automationSetting): bool
    {
        return $authUser->can('Replicate:AutomationSetting');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AutomationSetting');
    }

}