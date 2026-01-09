<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\HostelPayroll;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostelPayrollPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HostelPayroll');
    }

    public function view(AuthUser $authUser, HostelPayroll $hostelPayroll): bool
    {
        return $authUser->can('View:HostelPayroll');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HostelPayroll');
    }

    public function update(AuthUser $authUser, HostelPayroll $hostelPayroll): bool
    {
        return $authUser->can('Update:HostelPayroll');
    }

    public function delete(AuthUser $authUser, HostelPayroll $hostelPayroll): bool
    {
        return $authUser->can('Delete:HostelPayroll');
    }

    public function restore(AuthUser $authUser, HostelPayroll $hostelPayroll): bool
    {
        return $authUser->can('Restore:HostelPayroll');
    }

    public function forceDelete(AuthUser $authUser, HostelPayroll $hostelPayroll): bool
    {
        return $authUser->can('ForceDelete:HostelPayroll');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HostelPayroll');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HostelPayroll');
    }

    public function replicate(AuthUser $authUser, HostelPayroll $hostelPayroll): bool
    {
        return $authUser->can('Replicate:HostelPayroll');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HostelPayroll');
    }

}