<?php

declare(strict_types=1);

namespace Modules\PaymentsChannel\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\PaymentsChannel\Models\PayProviderConfig;
use Illuminate\Auth\Access\HandlesAuthorization;

class PayProviderConfigPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PayProviderConfig');
    }

    public function view(AuthUser $authUser, PayProviderConfig $payProviderConfig): bool
    {
        return $authUser->can('View:PayProviderConfig');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PayProviderConfig');
    }

    public function update(AuthUser $authUser, PayProviderConfig $payProviderConfig): bool
    {
        return $authUser->can('Update:PayProviderConfig');
    }

    public function delete(AuthUser $authUser, PayProviderConfig $payProviderConfig): bool
    {
        return $authUser->can('Delete:PayProviderConfig');
    }

    public function restore(AuthUser $authUser, PayProviderConfig $payProviderConfig): bool
    {
        return $authUser->can('Restore:PayProviderConfig');
    }

    public function forceDelete(AuthUser $authUser, PayProviderConfig $payProviderConfig): bool
    {
        return $authUser->can('ForceDelete:PayProviderConfig');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PayProviderConfig');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PayProviderConfig');
    }

    public function replicate(AuthUser $authUser, PayProviderConfig $payProviderConfig): bool
    {
        return $authUser->can('Replicate:PayProviderConfig');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PayProviderConfig');
    }

}