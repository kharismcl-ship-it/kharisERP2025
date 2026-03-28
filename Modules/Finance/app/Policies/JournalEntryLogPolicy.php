<?php

declare(strict_types=1);

namespace Modules\Finance\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Finance\Models\JournalEntryLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class JournalEntryLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:JournalEntryLog');
    }

    public function view(AuthUser $authUser, JournalEntryLog $journalEntryLog): bool
    {
        return $authUser->can('View:JournalEntryLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return false; // logs are system-generated only
    }

    public function update(AuthUser $authUser, JournalEntryLog $journalEntryLog): bool
    {
        return false; // immutable
    }

    public function delete(AuthUser $authUser, JournalEntryLog $journalEntryLog): bool
    {
        return false; // immutable
    }
}