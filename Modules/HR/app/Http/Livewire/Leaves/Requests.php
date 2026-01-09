<?php

namespace Modules\HR\Http\Livewire\Leaves;

use Livewire\Component;
use Modules\HR\Models\LeaveRequest;
use Modules\HR\Models\LeaveType;

class Requests extends Component
{
    public $leaveTypeId;

    public $startDate;

    public $endDate;

    public $reason;

    public $leaveRequests = [];

    public $leaveTypes = [];

    public function mount()
    {
        $this->leaveTypes = LeaveType::where('company_id', app('current_company_id'))
            ->where('is_active', true)
            ->get();
        $this->loadLeaveRequests();
    }

    public function loadLeaveRequests()
    {
        $employeeId = auth()->user()->employee->id ?? null;

        if ($employeeId) {
            $this->leaveRequests = LeaveRequest::where('employee_id', $employeeId)
                ->with(['leaveType'])
                ->orderBy('created_at', 'desc')
                ->get();
        }
    }

    public function submitLeaveRequest()
    {
        $this->validate([
            'leaveTypeId' => 'required|exists:hr_leave_types,id',
            'startDate' => 'required|date|before_or_equal:endDate',
            'endDate' => 'required|date|after_or_equal:startDate',
            'reason' => 'nullable|string',
        ]);

        $employeeId = auth()->user()->employee->id ?? null;

        if ($employeeId) {
            $companyId = app('current_company_id');

            LeaveRequest::create([
                'company_id' => $companyId,
                'employee_id' => $employeeId,
                'leave_type_id' => $this->leaveTypeId,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'total_days' => \Carbon\Carbon::parse($this->startDate)->diffInDays(\Carbon\Carbon::parse($this->endDate)) + 1,
                'reason' => $this->reason,
                'status' => 'pending',
            ]);

            $this->reset(['leaveTypeId', 'startDate', 'endDate', 'reason']);
            $this->loadLeaveRequests();
            session()->flash('message', 'Leave request submitted successfully.');
        }
    }

    public function render()
    {
        return view('hr::livewire.leaves.requests');
    }
}
