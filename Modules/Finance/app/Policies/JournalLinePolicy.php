<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\JournalLine;
use Illuminate\Auth\Access\HandlesAuthorization;

class JournalLinePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:JournalLine');
    }

    public function view(AuthUser $authUser, JournalLine $journalLine): bool
    {
        return $authUser->can('View:JournalLine');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:JournalLine');
    }

    public function update(AuthUser $authUser, JournalLine $journalLine): bool
    {
        return $authUser->can('Update:JournalLine');
    }

    public function delete(AuthUser $authUser, JournalLine $journalLine): bool
    {
        return $authUser->can('Delete:JournalLine');
    }

    public function restore(AuthUser $authUser, JournalLine $journalLine): bool
    {
        return $authUser->can('Restore:JournalLine');
    }

    public function forceDelete(AuthUser $authUser, JournalLine $journalLine): bool
    {
        return $authUser->can('ForceDelete:JournalLine');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:JournalLine');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:JournalLine');
    }

    public function replicate(AuthUser $authUser, JournalLine $journalLine): bool
    {
        return $authUser->can('Replicate:JournalLine');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:JournalLine');
    }

}