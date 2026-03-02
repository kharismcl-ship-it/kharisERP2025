<?php

declare(strict_types=1);

namespace Modules\PaymentsChannel\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\PaymentsChannel\Models\PayIntent;
use Illuminate\Auth\Access\HandlesAuthorization;

class PayIntentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PayIntent');
    }

    public function view(AuthUser $authUser, PayIntent $payIntent): bool
    {
        return $authUser->can('View:PayIntent');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PayIntent');
    }

    public function update(AuthUser $authUser, PayIntent $payIntent): bool
    {
        return $authUser->can('Update:PayIntent');
    }

    public function delete(AuthUser $authUser, PayIntent $payIntent): bool
    {
        return $authUser->can('Delete:PayIntent');
    }

    public function restore(AuthUser $authUser, PayIntent $payIntent): bool
    {
        return $authUser->can('Restore:PayIntent');
    }

    public function forceDelete(AuthUser $authUser, PayIntent $payIntent): bool
    {
        return $authUser->can('ForceDelete:PayIntent');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PayIntent');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PayIntent');
    }

    public function replicate(AuthUser $authUser, PayIntent $payIntent): bool
    {
        return $authUser->can('Replicate:PayIntent');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PayIntent');
    }

}