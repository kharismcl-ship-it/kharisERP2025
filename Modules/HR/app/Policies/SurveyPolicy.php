<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\Survey;

class SurveyPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Survey');
    }

    public function view(AuthUser $authUser, Survey $survey): bool
    {
        return $authUser->can('View:Survey');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Survey');
    }

    public function update(AuthUser $authUser, Survey $survey): bool
    {
        return $authUser->can('Update:Survey') && $survey->status === 'draft';
    }

    public function delete(AuthUser $authUser, Survey $survey): bool
    {
        return $authUser->can('Delete:Survey') && $survey->status === 'draft';
    }

    public function restore(AuthUser $authUser, Survey $survey): bool
    {
        return $authUser->can('Restore:Survey');
    }

    public function forceDelete(AuthUser $authUser, Survey $survey): bool
    {
        return $authUser->can('ForceDelete:Survey');
    }
}