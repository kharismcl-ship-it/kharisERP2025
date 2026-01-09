<?php

namespace Modules\HR\Http\Livewire;

use Livewire\Component;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveRequest;
use Modules\HR\Models\LeaveType;

class Leave extends Component
{
    public $leaveTypes = [];

    public $employees = [];

    public $employee_id;

    public $leave_type_id;

    public $start_date;

    public $end_date;

    public $reason;

    public $leaveRequests = [];

    public function mount()
    {
        $companyId = app('current_company_id');
        $this->leaveTypes = LeaveType::where('company_id', $companyId)->get();
        $this->employees = Employee::where('company_id', $companyId)
            ->where('status', 'active')
            ->get();
        $this->loadLeaveRequests();
    }

    public function loadLeaveRequests()
    {
        $companyId = app('current_company_id');
        $this->leaveRequests = LeaveRequest::where('company_id', $companyId)
            ->with(['employee', 'leaveType'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function submitLeaveRequest()
    {
        $this->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $companyId = app('current_company_id');

        LeaveRequest::create([
            'company_id' => $companyId,
            'employee_id' => $this->employee_id,
            'leave_type_id' => $this->leave_type_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'reason' => $this->reason,
            'status' => 'pending',
        ]);

        $this->reset(['employee_id', 'leave_type_id', 'start_date', 'end_date', 'reason']);
        $this->loadLeaveRequests();
        session()->flash('message', 'Leave request submitted successfully.');
    }

    public function approveLeaveRequest($id)
    {
        $companyId = app('current_company_id');
        $leaveRequest = LeaveRequest::where('company_id', $companyId)->findOrFail($id);
        $leaveRequest->update(['status' => 'approved']);
        $this->loadLeaveRequests();
        session()->flash('message', 'Leave request approved.');
    }

    public function rejectLeaveRequest($id)
    {
        $companyId = app('current_company_id');
        $leaveRequest = LeaveRequest::where('company_id', $companyId)->findOrFail($id);
        $leaveRequest->update(['status' => 'rejected']);
        $this->loadLeaveRequests();
        session()->flash('message', 'Leave request rejected.');
    }

    public function render()
    {
        return view('hr::leave');
    }
}
