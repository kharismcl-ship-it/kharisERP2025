<?php

declare(strict_types=1);

namespace Modules\Requisition\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Requisition\Models\RequisitionSchedule;

class RequisitionSchedulePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RequisitionSchedule');
    }

    public function view(AuthUser $authUser, RequisitionSchedule $schedule): bool
    {
        return $authUser->can('View:RequisitionSchedule');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RequisitionSchedule');
    }

    public function update(AuthUser $authUser, RequisitionSchedule $schedule): bool
    {
        return $authUser->can('Update:RequisitionSchedule');
    }

    public function delete(AuthUser $authUser, RequisitionSchedule $schedule): bool
    {
        return $authUser->can('Delete:RequisitionSchedule');
    }
}
