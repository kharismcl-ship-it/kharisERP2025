<?php

namespace Modules\HR\Http\Livewire;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveType;
use Modules\HR\Services\LeaveReportingService;

class LeaveReports extends Component
{
    use WithPagination;

    public $reportType = 'detailed';

    public $filters = [
        'start_date' => null,
        'end_date' => null,
        'status' => null,
        'leave_type_id' => null,
        'department_id' => null,
        'employee_id' => null,
    ];

    public $departments = [];

    public $leaveTypes = [];

    public $employees = [];

    public $reportData = [];

    public $summaryData = [];

    public $exportFormat = 'csv';

    protected $queryString = [
        'reportType' => ['except' => 'detailed'],
        'filters.start_date' => ['except' => null],
        'filters.end_date' => ['except' => null],
        'filters.status' => ['except' => null],
        'filters.leave_type_id' => ['except' => null],
        'filters.department_id' => ['except' => null],
        'filters.employee_id' => ['except' => null],
    ];

    public function mount()
    {
        $this->departments = Department::where('company_id', app('current_company_id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $this->leaveTypes = LeaveType::where('company_id', app('current_company_id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $this->employees = Employee::where('company_id', app('current_company_id'))
            ->where('employment_status', 'active')
            ->orderBy('first_name')
            ->get();

        $this->generateReport();
    }

    public function generateReport()
    {
        $service = app(LeaveReportingService::class);

        switch ($this->reportType) {
            case 'detailed':
                $this->reportData = $service->generateLeaveReport($this->filters);
                break;
            case 'summary':
                $this->summaryData = $service->generateLeaveSummaryReport($this->filters);
                break;
            case 'employee':
                if ($this->filters['employee_id']) {
                    $this->reportData = $service->generateEmployeeLeaveReport(
                        $this->filters['employee_id'],
                        $this->filters
                    );
                }
                break;
        }
    }

    public function updated($property, $value)
    {
        if (str_starts_with($property, 'filters.') || $property === 'reportType') {
            $this->generateReport();
        }
    }

    public function exportReport()
    {
        $service = app(LeaveReportingService::class);
        $filename = 'leave_report_'.$this->reportType;

        switch ($this->reportType) {
            case 'detailed':
                $data = collect($this->reportData);
                break;
            case 'summary':
                // Export overview data for summary reports
                $data = collect([$this->summaryData['overview']]);
                break;
            case 'employee':
                $data = collect($this->reportData['leave_history'] ?? []);
                break;
            default:
                $data = collect();
        }

        if ($data->isEmpty()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No data available for export.',
            ]);

            return;
        }

        if ($this->exportFormat === 'csv') {
            $filePath = $service->exportToCsv($data, $filename);

            return Storage::download(
                str_replace(storage_path('app/'), '', $filePath),
                basename($filePath)
            );
        }

        // For Excel format (would require PhpSpreadsheet implementation)
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'Excel export requires additional setup.',
        ]);
    }

    public function clearFilters()
    {
        $this->filters = [
            'start_date' => null,
            'end_date' => null,
            'status' => null,
            'leave_type_id' => null,
            'department_id' => null,
            'employee_id' => null,
        ];

        $this->generateReport();
    }

    public function render()
    {
        return view('hr::livewire.leave-reports', [
            'statusOptions' => [
                'pending' => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
            ],
            'reportTypeOptions' => [
                'detailed' => 'Detailed Report',
                'summary' => 'Summary Report',
                'employee' => 'Employee Report',
            ],
        ]);
    }
}
