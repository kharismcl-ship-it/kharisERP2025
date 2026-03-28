<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\SafetyIncident;

class SafetyIncidentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SafetyIncident');
    }

    public function view(AuthUser $authUser, SafetyIncident $safetyIncident): bool
    {
        return $authUser->can('View:SafetyIncident');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SafetyIncident');
    }

    public function update(AuthUser $authUser, SafetyIncident $safetyIncident): bool
    {
        return $authUser->can('Update:SafetyIncident');
    }

    public function delete(AuthUser $authUser, SafetyIncident $safetyIncident): bool
    {
        return $authUser->can('Delete:SafetyIncident');
    }

    public function restore(AuthUser $authUser, SafetyIncident $safetyIncident): bool
    {
        return $authUser->can('Restore:SafetyIncident');
    }

    public function forceDelete(AuthUser $authUser, SafetyIncident $safetyIncident): bool
    {
        return $authUser->can('ForceDelete:SafetyIncident');
    }
}