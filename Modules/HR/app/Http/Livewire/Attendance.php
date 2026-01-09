<?php

namespace Modules\HR\Http\Livewire;

use Livewire\Component;
use Modules\HR\Models\AttendanceRecord;
use Modules\HR\Models\Employee;

class Attendance extends Component
{
    public $date;

    public $employees = [];

    public $attendance = [];

    public function mount()
    {
        $this->date = now()->toDateString();
        $this->loadEmployees();
        $this->loadAttendance();
    }

    public function loadEmployees()
    {
        $companyId = app('current_company_id');
        $this->employees = Employee::where('company_id', $companyId)
            ->where('status', 'active')
            ->get();
    }

    public function loadAttendance()
    {
        $companyId = app('current_company_id');
        $records = AttendanceRecord::where('company_id', $companyId)
            ->where('date', $this->date)
            ->get()
            ->keyBy('employee_id');

        foreach ($this->employees as $employee) {
            $this->attendance[$employee->id] = [
                'status' => $records->has($employee->id) ? $records[$employee->id]->status : 'present',
                'check_in_time' => $records->has($employee->id) ? $records[$employee->id]->check_in_time : null,
                'check_out_time' => $records->has($employee->id) ? $records[$employee->id]->check_out_time : null,
            ];
        }
    }

    public function saveAttendance()
    {
        $companyId = app('current_company_id');

        foreach ($this->attendance as $employeeId => $data) {
            AttendanceRecord::updateOrCreate(
                [
                    'company_id' => $companyId,
                    'employee_id' => $employeeId,
                    'date' => $this->date,
                ],
                [
                    'status' => $data['status'],
                    'check_in_time' => $data['check_in_time'],
                    'check_out_time' => $data['check_out_time'],
                ]
            );
        }

        session()->flash('message', 'Attendance records saved successfully.');
    }

    public function render()
    {
        return view('hr::attendance');
    }
}
