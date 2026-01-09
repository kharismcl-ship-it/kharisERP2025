<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\WhatsAppGroupMessage;

class WhatsAppGroupMessagePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:WhatsAppGroupMessage');
    }

    public function view(AuthUser $authUser, WhatsAppGroupMessage $whatsAppGroupMessage): bool
    {
        return $authUser->can('View:WhatsAppGroupMessage');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:WhatsAppGroupMessage');
    }

    public function update(AuthUser $authUser, WhatsAppGroupMessage $whatsAppGroupMessage): bool
    {
        return $authUser->can('Update:WhatsAppGroupMessage');
    }

    public function delete(AuthUser $authUser, WhatsAppGroupMessage $whatsAppGroupMessage): bool
    {
        return $authUser->can('Delete:WhatsAppGroupMessage');
    }

    public function restore(AuthUser $authUser, WhatsAppGroupMessage $whatsAppGroupMessage): bool
    {
        return $authUser->can('Restore:WhatsAppGroupMessage');
    }

    public function forceDelete(AuthUser $authUser, WhatsAppGroupMessage $whatsAppGroupMessage): bool
    {
        return $authUser->can('ForceDelete:WhatsAppGroupMessage');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:WhatsAppGroupMessage');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:WhatsAppGroupMessage');
    }

    public function replicate(AuthUser $authUser, WhatsAppGroupMessage $whatsAppGroupMessage): bool
    {
        return $authUser->can('Replicate:WhatsAppGroupMessage');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:WhatsAppGroupMessage');
    }
}
