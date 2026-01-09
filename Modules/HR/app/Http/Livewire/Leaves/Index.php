<?php

namespace Modules\HR\Http\Livewire\Leaves;

use Livewire\Component;
use Modules\HR\Models\Department;
use Modules\HR\Models\LeaveRequest;
use Modules\HR\Models\LeaveType;

class Index extends Component
{
    public $search = '';

    public $status = '';

    public $leaveTypeId = '';

    public $departmentId = '';

    public $startDate = '';

    public $endDate = '';

    public $leaveRequests = [];

    public function mount()
    {
        $this->loadLeaveRequests();
    }

    public function loadLeaveRequests()
    {
        $companyId = app('current_company_id');

        $query = LeaveRequest::where('company_id', $companyId)
            ->with(['employee', 'leaveType']);

        if ($this->search) {
            $query->whereHas('employee', function ($q) {
                $q->where('first_name', 'like', '%'.$this->search.'%')
                    ->orWhere('last_name', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->leaveTypeId) {
            $query->where('leave_type_id', $this->leaveTypeId);
        }

        if ($this->departmentId) {
            $query->whereHas('employee', function ($q) {
                $q->where('department_id', $this->departmentId);
            });
        }

        if ($this->startDate) {
            $query->where('start_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('end_date', '<=', $this->endDate);
        }

        $this->leaveRequests = $query->paginate(10);
    }

    public function approveLeaveRequest($id)
    {
        $companyId = app('current_company_id');
        $leaveRequest = LeaveRequest::where('company_id', $companyId)->findOrFail($id);
        $leaveRequest->update([
            'status' => 'approved',
            'approved_by_employee_id' => auth()->user()->employee->id ?? null,
            'approved_at' => now(),
        ]);
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
        $leaveTypes = LeaveType::where('company_id', app('current_company_id'))->get();
        $departments = Department::where('company_id', app('current_company_id'))->get();

        return view('hr::livewire.leaves.index', [
            'leaveRequests' => $this->leaveRequests,
            'leaveTypes' => $leaveTypes,
            'departments' => $departments,
        ]);
    }
}
