<?php

namespace Modules\HR\Listeners;

use Modules\HR\Events\EmployeeCompanyAssignmentCreated;
use Modules\HR\Events\EmployeeCompanyAssignmentUpdated;

class SyncEmployeeRoles
{
    /**
     * Handle the event.
     *
     * @param  EmployeeCompanyAssignmentCreated|EmployeeCompanyAssignmentUpdated  $event
     * @return void
     */
    public function handle($event)
    {
        // Sync the employee's user roles based on their company assignments
        $event->assignment->employee->syncUserRoles();
    }
}
