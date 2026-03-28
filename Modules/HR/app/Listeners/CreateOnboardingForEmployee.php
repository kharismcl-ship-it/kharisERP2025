<?php

namespace Modules\HR\Listeners;

use Modules\HR\Events\NewEmployeeOnboarded;
use Modules\HR\Models\OnboardingTask;

class CreateOnboardingForEmployee
{
    public function handle(NewEmployeeOnboarded $event): void
    {
        $employee = $event->employee;

        // Only create onboarding tasks if none have been created yet
        $alreadyHasTasks = OnboardingTask::where('employee_id', $employee->id)->exists();
        if ($alreadyHasTasks) {
            return;
        }

        OnboardingTask::createFromTemplates($employee);
    }
}