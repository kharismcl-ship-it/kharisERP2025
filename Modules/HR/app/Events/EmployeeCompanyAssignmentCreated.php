<?php

namespace Modules\HR\Events;

use Illuminate\Queue\SerializesModels;
use Modules\HR\Models\EmployeeCompanyAssignment;

class EmployeeCompanyAssignmentCreated
{
    use SerializesModels;

    public EmployeeCompanyAssignment $assignment;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(EmployeeCompanyAssignment $assignment)
    {
        $this->assignment = $assignment;
    }
}
