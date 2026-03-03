<?php

declare(strict_types=1);

namespace Modules\Farms\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Farms\Models\FarmDocument;
use Illuminate\Auth\Access\HandlesAuthorization;

class FarmDocumentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FarmDocument');
    }

    public function view(AuthUser $authUser, FarmDocument $record): bool
    {
        return $authUser->can('View:FarmDocument');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FarmDocument');
    }

    public function update(AuthUser $authUser, FarmDocument $record): bool
    {
        return $authUser->can('Update:FarmDocument');
    }

    public function delete(AuthUser $authUser, FarmDocument $record): bool
    {
        return $authUser->can('Delete:FarmDocument');
    }

    public function restore(AuthUser $authUser, FarmDocument $record): bool
    {
        return $authUser->can('Restore:FarmDocument');
    }

    public function forceDelete(AuthUser $authUser, FarmDocument $record): bool
    {
        return $authUser->can('ForceDelete:FarmDocument');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FarmDocument');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FarmDocument');
    }

    public function replicate(AuthUser $authUser, FarmDocument $record): bool
    {
        return $authUser->can('Replicate:FarmDocument');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FarmDocument');
    }
}
