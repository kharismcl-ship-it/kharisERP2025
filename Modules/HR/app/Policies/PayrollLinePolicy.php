<?php

declare(strict_types=1);

namespace Modules\HR\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\HR\Models\PayrollLine;

class PayrollLinePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PayrollLine');
    }

    public function view(AuthUser $authUser, PayrollLine $payrollLine): bool
    {
        return $authUser->can('View:PayrollLine');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PayrollLine');
    }

    public function update(AuthUser $authUser, PayrollLine $payrollLine): bool
    {
        return $authUser->can('Update:PayrollLine');
    }

    public function delete(AuthUser $authUser, PayrollLine $payrollLine): bool
    {
        return $authUser->can('Delete:PayrollLine');
    }

    public function restore(AuthUser $authUser, PayrollLine $payrollLine): bool
    {
        return $authUser->can('Restore:PayrollLine');
    }

    public function forceDelete(AuthUser $authUser, PayrollLine $payrollLine): bool
    {
        return $authUser->can('ForceDelete:PayrollLine');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PayrollLine');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PayrollLine');
    }

    public function replicate(AuthUser $authUser, PayrollLine $payrollLine): bool
    {
        return $authUser->can('Replicate:PayrollLine');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PayrollLine');
    }
}
