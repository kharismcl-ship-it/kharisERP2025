<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\SkillCategory;

class SkillCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SkillCategory');
    }

    public function view(AuthUser $authUser, SkillCategory $skillCategory): bool
    {
        return $authUser->can('View:SkillCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SkillCategory');
    }

    public function update(AuthUser $authUser, SkillCategory $skillCategory): bool
    {
        return $authUser->can('Update:SkillCategory');
    }

    public function delete(AuthUser $authUser, SkillCategory $skillCategory): bool
    {
        return $authUser->can('Delete:SkillCategory');
    }

    public function restore(AuthUser $authUser, SkillCategory $skillCategory): bool
    {
        return $authUser->can('Restore:SkillCategory');
    }

    public function forceDelete(AuthUser $authUser, SkillCategory $skillCategory): bool
    {
        return $authUser->can('ForceDelete:SkillCategory');
    }
}