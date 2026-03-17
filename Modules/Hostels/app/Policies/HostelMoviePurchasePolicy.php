<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\HostelMoviePurchase;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostelMoviePurchasePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HostelMoviePurchase');
    }

    public function view(AuthUser $authUser, HostelMoviePurchase $hostelMoviePurchase): bool
    {
        return $authUser->can('View:HostelMoviePurchase');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HostelMoviePurchase');
    }

    public function update(AuthUser $authUser, HostelMoviePurchase $hostelMoviePurchase): bool
    {
        return $authUser->can('Update:HostelMoviePurchase');
    }

    public function delete(AuthUser $authUser, HostelMoviePurchase $hostelMoviePurchase): bool
    {
        return $authUser->can('Delete:HostelMoviePurchase');
    }

    public function restore(AuthUser $authUser, HostelMoviePurchase $hostelMoviePurchase): bool
    {
        return $authUser->can('Restore:HostelMoviePurchase');
    }

    public function forceDelete(AuthUser $authUser, HostelMoviePurchase $hostelMoviePurchase): bool
    {
        return $authUser->can('ForceDelete:HostelMoviePurchase');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HostelMoviePurchase');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HostelMoviePurchase');
    }

    public function replicate(AuthUser $authUser, HostelMoviePurchase $hostelMoviePurchase): bool
    {
        return $authUser->can('Replicate:HostelMoviePurchase');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HostelMoviePurchase');
    }

}