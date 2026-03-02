<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\DisciplinaryCase;
use Illuminate\Auth\Access\HandlesAuthorization;

class DisciplinaryCasePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DisciplinaryCase');
    }

    public function view(AuthUser $authUser, DisciplinaryCase $disciplinaryCase): bool
    {
        return $authUser->can('View:DisciplinaryCase');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DisciplinaryCase');
    }

    public function update(AuthUser $authUser, DisciplinaryCase $disciplinaryCase): bool
    {
        return $authUser->can('Update:DisciplinaryCase');
    }

    public function delete(AuthUser $authUser, DisciplinaryCase $disciplinaryCase): bool
    {
        return $authUser->can('Delete:DisciplinaryCase');
    }

    public function restore(AuthUser $authUser, DisciplinaryCase $disciplinaryCase): bool
    {
        return $authUser->can('Restore:DisciplinaryCase');
    }

    public function forceDelete(AuthUser $authUser, DisciplinaryCase $disciplinaryCase): bool
    {
        return $authUser->can('ForceDelete:DisciplinaryCase');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DisciplinaryCase');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DisciplinaryCase');
    }

    public function replicate(AuthUser $authUser, DisciplinaryCase $disciplinaryCase): bool
    {
        return $authUser->can('Replicate:DisciplinaryCase');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DisciplinaryCase');
    }

}