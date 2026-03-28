<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\OnboardingTask;

class OnboardingTaskPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:OnboardingTask');
    }

    public function view(AuthUser $authUser, OnboardingTask $onboardingTask): bool
    {
        return $authUser->can('View:OnboardingTask');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:OnboardingTask');
    }

    public function update(AuthUser $authUser, OnboardingTask $onboardingTask): bool
    {
        return $authUser->can('Update:OnboardingTask');
    }

    public function delete(AuthUser $authUser, OnboardingTask $onboardingTask): bool
    {
        return $authUser->can('Delete:OnboardingTask');
    }

    public function restore(AuthUser $authUser, OnboardingTask $onboardingTask): bool
    {
        return $authUser->can('Restore:OnboardingTask');
    }

    public function forceDelete(AuthUser $authUser, OnboardingTask $onboardingTask): bool
    {
        return $authUser->can('ForceDelete:OnboardingTask');
    }
}