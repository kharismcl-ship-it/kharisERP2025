<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\PricingPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class PricingPolicyPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PricingPolicy');
    }

    public function view(AuthUser $authUser, PricingPolicy $pricingPolicy): bool
    {
        return $authUser->can('View:PricingPolicy');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PricingPolicy');
    }

    public function update(AuthUser $authUser, PricingPolicy $pricingPolicy): bool
    {
        return $authUser->can('Update:PricingPolicy');
    }

    public function delete(AuthUser $authUser, PricingPolicy $pricingPolicy): bool
    {
        return $authUser->can('Delete:PricingPolicy');
    }

    public function restore(AuthUser $authUser, PricingPolicy $pricingPolicy): bool
    {
        return $authUser->can('Restore:PricingPolicy');
    }

    public function forceDelete(AuthUser $authUser, PricingPolicy $pricingPolicy): bool
    {
        return $authUser->can('ForceDelete:PricingPolicy');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PricingPolicy');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PricingPolicy');
    }

    public function replicate(AuthUser $authUser, PricingPolicy $pricingPolicy): bool
    {
        return $authUser->can('Replicate:PricingPolicy');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PricingPolicy');
    }

}