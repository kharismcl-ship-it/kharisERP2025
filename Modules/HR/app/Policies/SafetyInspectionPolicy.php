<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\SafetyInspection;

class SafetyInspectionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SafetyInspection');
    }

    public function view(AuthUser $authUser, SafetyInspection $safetyInspection): bool
    {
        return $authUser->can('View:SafetyInspection');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SafetyInspection');
    }

    public function update(AuthUser $authUser, SafetyInspection $safetyInspection): bool
    {
        return $authUser->can('Update:SafetyInspection');
    }

    public function delete(AuthUser $authUser, SafetyInspection $safetyInspection): bool
    {
        return $authUser->can('Delete:SafetyInspection');
    }

    public function restore(AuthUser $authUser, SafetyInspection $safetyInspection): bool
    {
        return $authUser->can('Restore:SafetyInspection');
    }

    public function forceDelete(AuthUser $authUser, SafetyInspection $safetyInspection): bool
    {
        return $authUser->can('ForceDelete:SafetyInspection');
    }
}