<?php

namespace Modules\HR\Services;

use Illuminate\Support\Facades\Log;
use Modules\CommunicationCentre\Services\CommunicationService;
use Modules\HR\Models\DisciplinaryCase;
use Modules\HR\Models\Employee;
use Modules\HR\Models\GrievanceCase;

class WorkplaceRelationsService
{
    protected CommunicationService $comm;

    public function __construct(CommunicationService $comm)
    {
        $this->comm = $comm;
    }

    // -------------------------------------------------------------------------
    // Disciplinary
    // -------------------------------------------------------------------------

    /**
     * Assign a disciplinary case to an HR handler and notify them.
     */
    public function assignDisciplinaryCase(DisciplinaryCase $case, Employee $handler): void
    {
        $case->update([
            'handled_by_employee_id' => $handler->id,
            'status'                 => 'under_review',
        ]);

        $this->notifyHandler($handler, [
            'handler_name'       => $handler->full_name,
            'employee_name'      => $case->employee?->full_name ?? 'N/A',
            'case_type'          => DisciplinaryCase::TYPES[$case->type] ?? $case->type,
            'incident_date'      => $case->incident_date?->format('M j, Y') ?? '—',
            'incident_description' => $case->incident_description,
        ], 'hr_disciplinary_assigned');
    }

    /**
     * Resolve a disciplinary case and notify the employee.
     */
    public function resolveDisciplinaryCase(DisciplinaryCase $case, string $resolution, ?string $notes = null): void
    {
        $case->update([
            'status'           => 'resolved',
            'resolution_notes' => $notes,
            'resolution_date'  => now(),
        ]);

        $employee = $case->employee;
        if ($employee) {
            $this->notifyEmployee($employee, [
                'employee_name' => $employee->full_name,
                'case_type'     => DisciplinaryCase::TYPES[$case->type] ?? $case->type,
                'resolution'    => $resolution,
                'resolved_on'   => now()->format('M j, Y'),
            ], 'hr_disciplinary_resolved');
        }
    }

    // -------------------------------------------------------------------------
    // Grievance
    // -------------------------------------------------------------------------

    /**
     * Assign a grievance case to an HR handler and notify them.
     */
    public function assignGrievanceCase(GrievanceCase $case, Employee $handler): void
    {
        $case->update([
            'assigned_to_employee_id' => $handler->id,
            'status'                  => 'under_investigation',
        ]);

        $this->notifyHandler($handler, [
            'handler_name'  => $handler->full_name,
            'grievance_type' => $case->grievance_type,
            'filed_date'    => $case->filed_date?->format('M j, Y') ?? '—',
            'description'   => $case->is_anonymous ? '[Anonymous]' : $case->description,
        ], 'hr_grievance_assigned');
    }

    /**
     * Resolve a grievance case and notify the complainant (unless anonymous).
     */
    public function resolveGrievanceCase(GrievanceCase $case, string $resolution): void
    {
        $case->update([
            'status'          => 'resolved',
            'resolution'      => $resolution,
            'resolution_date' => now(),
        ]);

        if (! $case->is_anonymous && $case->employee) {
            $this->notifyEmployee($case->employee, [
                'employee_name' => $case->employee->full_name,
                'grievance_type' => $case->grievance_type,
                'resolution'    => $resolution,
                'resolved_on'   => now()->format('M j, Y'),
            ], 'hr_grievance_resolved');
        }
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function notifyHandler(Employee $handler, array $data, string $templateCode): void
    {
        try {
            $this->comm->sendToModelThroughEnabledChannels($handler, $templateCode, $data);
        } catch (\Throwable $e) {
            Log::warning("WorkplaceRelationsService: notification failed [{$templateCode}]", [
                'handler_id' => $handler->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    private function notifyEmployee(Employee $employee, array $data, string $templateCode): void
    {
        try {
            $this->comm->sendToModelThroughEnabledChannels($employee, $templateCode, $data);
        } catch (\Throwable $e) {
            Log::warning("WorkplaceRelationsService: notification failed [{$templateCode}]", [
                'employee_id' => $employee->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }
}
