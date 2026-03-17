<?php

namespace Modules\HR\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\HR\Events\NewEmployeeOnboarded;

class SendWelcomeEmail
{
    public function __construct(
        protected CommunicationService $communicationService
    ) {}

    public function handle(NewEmployeeOnboarded $event): void
    {
        $employee = $event->employee;

        if (! $employee->email) {
            return;
        }

        try {
            $body = "Welcome to the team, {$employee->full_name}!\n\n"
                . "Your employee record has been set up. Please log in to the HR portal to review your details.\n\n"
                . "If you have any questions, please contact the HR department.";

            $this->communicationService->sendRawEmail(
                $employee->email,
                $employee->full_name,
                'Welcome to the Team!',
                $body,
            );
        } catch (\Throwable $e) {
            Log::warning('SendWelcomeEmail: Failed to send welcome email.', [
                'employee_id' => $employee->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }
}
