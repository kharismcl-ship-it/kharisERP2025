<?php

declare(strict_types=1);

namespace Modules\Requisition\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Requisition\Models\RequisitionCustomField;

class RequisitionCustomFieldPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RequisitionCustomField');
    }

    public function view(AuthUser $authUser, RequisitionCustomField $field): bool
    {
        return $authUser->can('View:RequisitionCustomField');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RequisitionCustomField');
    }

    public function update(AuthUser $authUser, RequisitionCustomField $field): bool
    {
        return $authUser->can('Update:RequisitionCustomField');
    }

    public function delete(AuthUser $authUser, RequisitionCustomField $field): bool
    {
        return $authUser->can('Delete:RequisitionCustomField');
    }
}