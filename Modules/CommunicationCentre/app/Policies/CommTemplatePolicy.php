<?php

declare(strict_types=1);

namespace Modules\CommunicationCentre\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\CommunicationCentre\Models\CommTemplate;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommTemplatePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CommTemplate');
    }

    public function view(AuthUser $authUser, CommTemplate $commTemplate): bool
    {
        return $authUser->can('View:CommTemplate');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CommTemplate');
    }

    public function update(AuthUser $authUser, CommTemplate $commTemplate): bool
    {
        return $authUser->can('Update:CommTemplate');
    }

    public function delete(AuthUser $authUser, CommTemplate $commTemplate): bool
    {
        return $authUser->can('Delete:CommTemplate');
    }

    public function restore(AuthUser $authUser, CommTemplate $commTemplate): bool
    {
        return $authUser->can('Restore:CommTemplate');
    }

    public function forceDelete(AuthUser $authUser, CommTemplate $commTemplate): bool
    {
        return $authUser->can('ForceDelete:CommTemplate');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CommTemplate');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CommTemplate');
    }

    public function replicate(AuthUser $authUser, CommTemplate $commTemplate): bool
    {
        return $authUser->can('Replicate:CommTemplate');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CommTemplate');
    }

}