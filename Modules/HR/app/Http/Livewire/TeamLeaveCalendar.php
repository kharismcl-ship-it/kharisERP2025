<?php

namespace Modules\HR\Http\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveRequest;

class TeamLeaveCalendar extends Component
{
    public $currentDate;

    public $viewMode = 'month'; // month, week, day

    public $selectedDepartment = null;

    public $selectedEmployee = null;

    public $showApprovedOnly = true;

    public $departments = [];

    public $employees = [];

    protected $queryString = [
        'viewMode' => ['except' => 'month'],
        'selectedDepartment' => ['except' => null],
        'selectedEmployee' => ['except' => null],
        'showApprovedOnly' => ['except' => true],
    ];

    public function mount()
    {
        $this->currentDate = now()->toDateString();
        $this->departments = Department::where('company_id', app('current_company_id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $this->employees = Employee::where('company_id', app('current_company_id'))
            ->where('employment_status', 'active')
            ->orderBy('first_name')
            ->get();
    }

    public function getEventsProperty()
    {
        $query = LeaveRequest::with(['employee', 'leaveType'])
            ->where('company_id', app('current_company_id'))
            ->where('status', $this->showApprovedOnly ? 'approved' : '!=', 'rejected')
            ->where(function ($query) {
                $query->where('start_date', '<=', $this->getEndDate())
                    ->where('end_date', '>=', $this->getStartDate());
            });

        if ($this->selectedDepartment) {
            $query->whereHas('employee', function ($q) {
                $q->where('department_id', $this->selectedDepartment);
            });
        }

        if ($this->selectedEmployee) {
            $query->where('employee_id', $this->selectedEmployee);
        }

        $leaveRequests = $query->get();

        return $leaveRequests->map(function ($request) {
            return [
                'id' => $request->id,
                'title' => $request->employee->full_name.' - '.$request->leaveType->name,
                'start' => $request->start_date,
                'end' => Carbon::parse($request->end_date)->addDay()->toDateString(), // FullCalendar uses exclusive end
                'color' => $this->getLeaveColor($request->leaveType->name),
                'extendedProps' => [
                    'employee' => $request->employee->full_name,
                    'leave_type' => $request->leaveType->name,
                    'status' => $request->status,
                    'days' => $request->total_days,
                    'reason' => $request->reason,
                ],
            ];
        })->toArray();
    }

    protected function getLeaveColor($leaveType)
    {
        $colors = [
            'Annual Leave' => '#10B981', // green
            'Sick Leave' => '#EF4444',   // red
            'Maternity Leave' => '#8B5CF6', // purple
            'Paternity Leave' => '#3B82F6', // blue
            'Emergency Leave' => '#F59E0B', // amber
            'Unpaid Leave' => '#6B7280', // gray
        ];

        return $colors[$leaveType] ?? '#9CA3AF'; // default gray
    }

    protected function getStartDate()
    {
        return match ($this->viewMode) {
            'month' => Carbon::parse($this->currentDate)->startOfMonth()->toDateString(),
            'week' => Carbon::parse($this->currentDate)->startOfWeek()->toDateString(),
            'day' => $this->currentDate,
        };
    }

    protected function getEndDate()
    {
        return match ($this->viewMode) {
            'month' => Carbon::parse($this->currentDate)->endOfMonth()->toDateString(),
            'week' => Carbon::parse($this->currentDate)->endOfWeek()->toDateString(),
            'day' => $this->currentDate,
        };
    }

    public function previousPeriod()
    {
        $this->currentDate = match ($this->viewMode) {
            'month' => Carbon::parse($this->currentDate)->subMonth()->toDateString(),
            'week' => Carbon::parse($this->currentDate)->subWeek()->toDateString(),
            'day' => Carbon::parse($this->currentDate)->subDay()->toDateString(),
        };
    }

    public function nextPeriod()
    {
        $this->currentDate = match ($this->viewMode) {
            'month' => Carbon::parse($this->currentDate)->addMonth()->toDateString(),
            'week' => Carbon::parse($this->currentDate)->addWeek()->toDateString(),
            'day' => Carbon::parse($this->currentDate)->addDay()->toDateString(),
        };
    }

    public function today()
    {
        $this->currentDate = now()->toDateString();
    }

    public function changeView($view)
    {
        $this->viewMode = $view;
    }

    public function render()
    {
        $periodLabel = match ($this->viewMode) {
            'month' => Carbon::parse($this->currentDate)->format('F Y'),
            'week' => Carbon::parse($this->currentDate)->startOfWeek()->format('M j').' - '.
                     Carbon::parse($this->currentDate)->endOfWeek()->format('M j, Y'),
            'day' => Carbon::parse($this->currentDate)->format('l, F j, Y'),
        };

        return view('hr::livewire.team-leave-calendar', [
            'events' => $this->events,
            'periodLabel' => $periodLabel,
        ]);
    }
}
