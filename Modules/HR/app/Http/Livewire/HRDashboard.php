<?php

namespace Modules\HR\Http\Livewire;

use Livewire\Component;
use Modules\HR\Models\AttendanceRecord;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveRequest;

class HRDashboard extends Component
{
    public function render()
    {
        $companyId = app('current_company_id');

        $totalEmployees = Employee::where('company_id', $companyId)->count();
        $departments = Department::where('company_id', $companyId)->count();

        $onLeaveToday = Employee::where('company_id', $companyId)
            ->whereHas('leaveRequests', function ($query) {
                $query->where('status', 'approved')
                    ->where('start_date', '<=', now()->toDateString())
                    ->where('end_date', '>=', now()->toDateString());
            })
            ->count();

        $presentToday = AttendanceRecord::where('company_id', $companyId)
            ->where('date', now()->toDateString())
            ->where('status', 'present')
            ->count();

        $pendingLeaveRequests = LeaveRequest::where('company_id', $companyId)
            ->where('status', 'pending')
            ->count();

        return view('hr::livewire.hr-dashboard', [
            'totalEmployees' => $totalEmployees,
            'departments' => $departments,
            'onLeaveToday' => $onLeaveToday,
            'presentToday' => $presentToday,
            'pendingLeaveRequests' => $pendingLeaveRequests,
        ]);
    }
}
