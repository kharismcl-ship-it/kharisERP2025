<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\HostelWhatsAppGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostelWhatsAppGroupPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HostelWhatsAppGroup');
    }

    public function view(AuthUser $authUser, HostelWhatsAppGroup $hostelWhatsAppGroup): bool
    {
        return $authUser->can('View:HostelWhatsAppGroup');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HostelWhatsAppGroup');
    }

    public function update(AuthUser $authUser, HostelWhatsAppGroup $hostelWhatsAppGroup): bool
    {
        return $authUser->can('Update:HostelWhatsAppGroup');
    }

    public function delete(AuthUser $authUser, HostelWhatsAppGroup $hostelWhatsAppGroup): bool
    {
        return $authUser->can('Delete:HostelWhatsAppGroup');
    }

    public function restore(AuthUser $authUser, HostelWhatsAppGroup $hostelWhatsAppGroup): bool
    {
        return $authUser->can('Restore:HostelWhatsAppGroup');
    }

    public function forceDelete(AuthUser $authUser, HostelWhatsAppGroup $hostelWhatsAppGroup): bool
    {
        return $authUser->can('ForceDelete:HostelWhatsAppGroup');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HostelWhatsAppGroup');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HostelWhatsAppGroup');
    }

    public function replicate(AuthUser $authUser, HostelWhatsAppGroup $hostelWhatsAppGroup): bool
    {
        return $authUser->can('Replicate:HostelWhatsAppGroup');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HostelWhatsAppGroup');
    }

}