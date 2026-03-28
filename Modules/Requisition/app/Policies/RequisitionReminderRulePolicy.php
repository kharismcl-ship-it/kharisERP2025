<?php

declare(strict_types=1);

namespace Modules\Requisition\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Requisition\Models\RequisitionReminderRule;

class RequisitionReminderRulePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RequisitionReminderRule');
    }

    public function view(AuthUser $authUser, RequisitionReminderRule $rule): bool
    {
        return $authUser->can('View:RequisitionReminderRule');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RequisitionReminderRule');
    }

    public function update(AuthUser $authUser, RequisitionReminderRule $rule): bool
    {
        return $authUser->can('Update:RequisitionReminderRule');
    }

    public function delete(AuthUser $authUser, RequisitionReminderRule $rule): bool
    {
        return $authUser->can('Delete:RequisitionReminderRule');
    }
}