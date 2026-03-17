<?php

namespace Modules\HR\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\HR\Events\EmployeeCompanyAssignmentCreated;
use Modules\HR\Events\EmployeeCompanyAssignmentUpdated;
use Modules\HR\Events\LeaveAccrued;
use Modules\HR\Events\NewEmployeeOnboarded;
use Modules\HR\Events\PayrollFinalized;
use Modules\HR\Listeners\NotifyLeaveAccrued;
use Modules\HR\Listeners\PostPayrollToFinance;
use Modules\HR\Listeners\SendPayslipEmails;
use Modules\HR\Listeners\SendWelcomeEmail;
use Modules\HR\Listeners\SyncEmployeeRoles;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        EmployeeCompanyAssignmentCreated::class => [
            SyncEmployeeRoles::class,
        ],
        EmployeeCompanyAssignmentUpdated::class => [
            SyncEmployeeRoles::class,
        ],
        NewEmployeeOnboarded::class => [
            SendWelcomeEmail::class,
        ],
        PayrollFinalized::class => [
            PostPayrollToFinance::class,
            SendPayslipEmails::class,
        ],
        LeaveAccrued::class => [
            NotifyLeaveAccrued::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
