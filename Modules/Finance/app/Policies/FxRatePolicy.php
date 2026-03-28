<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\FxRate;
use Illuminate\Auth\Access\HandlesAuthorization;

class FxRatePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FxRate');
    }

    public function view(AuthUser $authUser, FxRate $fxRate): bool
    {
        return $authUser->can('View:FxRate');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FxRate');
    }

    public function update(AuthUser $authUser, FxRate $fxRate): bool
    {
        return $authUser->can('Update:FxRate');
    }

    public function delete(AuthUser $authUser, FxRate $fxRate): bool
    {
        return $authUser->can('Delete:FxRate');
    }

    public function restore(AuthUser $authUser, FxRate $fxRate): bool
    {
        return $authUser->can('Restore:FxRate');
    }

    public function forceDelete(AuthUser $authUser, FxRate $fxRate): bool
    {
        return $authUser->can('ForceDelete:FxRate');
    }
}