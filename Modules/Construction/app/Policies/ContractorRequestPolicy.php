<?php

declare(strict_types=1);

namespace Modules\Construction\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Construction\Models\ContractorRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContractorRequestPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ContractorRequest');
    }

    public function view(AuthUser $authUser, ContractorRequest $contractorRequest): bool
    {
        return $authUser->can('View:ContractorRequest');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ContractorRequest');
    }

    public function update(AuthUser $authUser, ContractorRequest $contractorRequest): bool
    {
        return $authUser->can('Update:ContractorRequest');
    }

    public function delete(AuthUser $authUser, ContractorRequest $contractorRequest): bool
    {
        return $authUser->can('Delete:ContractorRequest');
    }

    public function restore(AuthUser $authUser, ContractorRequest $contractorRequest): bool
    {
        return $authUser->can('Restore:ContractorRequest');
    }

    public function forceDelete(AuthUser $authUser, ContractorRequest $contractorRequest): bool
    {
        return $authUser->can('ForceDelete:ContractorRequest');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ContractorRequest');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ContractorRequest');
    }

    public function replicate(AuthUser $authUser, ContractorRequest $contractorRequest): bool
    {
        return $authUser->can('Replicate:ContractorRequest');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ContractorRequest');
    }

}