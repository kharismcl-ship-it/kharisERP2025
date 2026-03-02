<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\Certification;
use Illuminate\Auth\Access\HandlesAuthorization;

class CertificationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Certification');
    }

    public function view(AuthUser $authUser, Certification $certification): bool
    {
        return $authUser->can('View:Certification');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Certification');
    }

    public function update(AuthUser $authUser, Certification $certification): bool
    {
        return $authUser->can('Update:Certification');
    }

    public function delete(AuthUser $authUser, Certification $certification): bool
    {
        return $authUser->can('Delete:Certification');
    }

    public function restore(AuthUser $authUser, Certification $certification): bool
    {
        return $authUser->can('Restore:Certification');
    }

    public function forceDelete(AuthUser $authUser, Certification $certification): bool
    {
        return $authUser->can('ForceDelete:Certification');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Certification');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Certification');
    }

    public function replicate(AuthUser $authUser, Certification $certification): bool
    {
        return $authUser->can('Replicate:Certification');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Certification');
    }

}