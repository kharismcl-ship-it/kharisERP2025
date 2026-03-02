<?php

declare(strict_types=1);

namespace Modules\Sales\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Sales\Models\PosTerminal;
use Illuminate\Auth\Access\HandlesAuthorization;

class PosTerminalPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PosTerminal');
    }

    public function view(AuthUser $authUser, PosTerminal $posTerminal): bool
    {
        return $authUser->can('View:PosTerminal');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PosTerminal');
    }

    public function update(AuthUser $authUser, PosTerminal $posTerminal): bool
    {
        return $authUser->can('Update:PosTerminal');
    }

    public function delete(AuthUser $authUser, PosTerminal $posTerminal): bool
    {
        return $authUser->can('Delete:PosTerminal');
    }

    public function restore(AuthUser $authUser, PosTerminal $posTerminal): bool
    {
        return $authUser->can('Restore:PosTerminal');
    }

    public function forceDelete(AuthUser $authUser, PosTerminal $posTerminal): bool
    {
        return $authUser->can('ForceDelete:PosTerminal');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PosTerminal');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PosTerminal');
    }

    public function replicate(AuthUser $authUser, PosTerminal $posTerminal): bool
    {
        return $authUser->can('Replicate:PosTerminal');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PosTerminal');
    }

}