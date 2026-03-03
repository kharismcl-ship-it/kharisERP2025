<?php

declare(strict_types=1);

namespace Modules\ManufacturingPaper\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ManufacturingPaper\Models\MpProductionLine;
use Illuminate\Auth\Access\HandlesAuthorization;

class MpProductionLinePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MpProductionLine');
    }

    public function view(AuthUser $authUser, MpProductionLine $record): bool
    {
        return $authUser->can('View:MpProductionLine');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MpProductionLine');
    }

    public function update(AuthUser $authUser, MpProductionLine $record): bool
    {
        return $authUser->can('Update:MpProductionLine');
    }

    public function delete(AuthUser $authUser, MpProductionLine $record): bool
    {
        return $authUser->can('Delete:MpProductionLine');
    }

    public function restore(AuthUser $authUser, MpProductionLine $record): bool
    {
        return $authUser->can('Restore:MpProductionLine');
    }

    public function forceDelete(AuthUser $authUser, MpProductionLine $record): bool
    {
        return $authUser->can('ForceDelete:MpProductionLine');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MpProductionLine');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MpProductionLine');
    }

    public function replicate(AuthUser $authUser, MpProductionLine $record): bool
    {
        return $authUser->can('Replicate:MpProductionLine');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MpProductionLine');
    }
}
