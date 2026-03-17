<?php

declare(strict_types=1);

namespace Modules\ClientService\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ClientService\Models\CsVisitor;
use Illuminate\Auth\Access\HandlesAuthorization;

class CsVisitorPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CsVisitor');
    }

    public function view(AuthUser $authUser, CsVisitor $csVisitor): bool
    {
        return $authUser->can('View:CsVisitor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CsVisitor');
    }

    public function update(AuthUser $authUser, CsVisitor $csVisitor): bool
    {
        return $authUser->can('Update:CsVisitor');
    }

    public function delete(AuthUser $authUser, CsVisitor $csVisitor): bool
    {
        return $authUser->can('Delete:CsVisitor');
    }

    public function restore(AuthUser $authUser, CsVisitor $csVisitor): bool
    {
        return $authUser->can('Restore:CsVisitor');
    }

    public function forceDelete(AuthUser $authUser, CsVisitor $csVisitor): bool
    {
        return $authUser->can('ForceDelete:CsVisitor');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CsVisitor');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CsVisitor');
    }

    public function replicate(AuthUser $authUser, CsVisitor $csVisitor): bool
    {
        return $authUser->can('Replicate:CsVisitor');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CsVisitor');
    }

}