<?php

declare(strict_types=1);

namespace Modules\Core\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Core\Models\AutomationLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class AutomationLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AutomationLog');
    }

    public function view(AuthUser $authUser, AutomationLog $automationLog): bool
    {
        return $authUser->can('View:AutomationLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AutomationLog');
    }

    public function update(AuthUser $authUser, AutomationLog $automationLog): bool
    {
        return $authUser->can('Update:AutomationLog');
    }

    public function delete(AuthUser $authUser, AutomationLog $automationLog): bool
    {
        return $authUser->can('Delete:AutomationLog');
    }

    public function restore(AuthUser $authUser, AutomationLog $automationLog): bool
    {
        return $authUser->can('Restore:AutomationLog');
    }

    public function forceDelete(AuthUser $authUser, AutomationLog $automationLog): bool
    {
        return $authUser->can('ForceDelete:AutomationLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AutomationLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AutomationLog');
    }

    public function replicate(AuthUser $authUser, AutomationLog $automationLog): bool
    {
        return $authUser->can('Replicate:AutomationLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AutomationLog');
    }

}