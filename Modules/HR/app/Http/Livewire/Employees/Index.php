<?php

namespace Modules\HR\Http\Livewire\Employees;

use Livewire\Component;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;

class Index extends Component
{
    public $search = '';

    public $departmentId = '';

    public $employmentStatus = '';

    public $employmentType = '';

    public $employees = [];

    public function mount()
    {
        $this->loadEmployees();
    }

    public function loadEmployees()
    {
        $companyId = app('current_company_id');

        $query = Employee::where('company_id', $companyId)
            ->with(['department', 'jobPosition']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%'.$this->search.'%')
                    ->orWhere('last_name', 'like', '%'.$this->search.'%')
                    ->orWhere('employee_code', 'like', '%'.$this->search.'%')
                    ->orWhere('phone', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->departmentId) {
            $query->where('department_id', $this->departmentId);
        }

        if ($this->employmentStatus) {
            $query->where('employment_status', $this->employmentStatus);
        }

        if ($this->employmentType) {
            $query->where('employment_type', $this->employmentType);
        }

        $this->employees = $query->paginate(10);
    }

    public function render()
    {
        $departments = Department::where('company_id', app('current_company_id'))->get();

        return view('hr::livewire.employees.index', [
            'employees' => $this->employees,
            'departments' => $departments,
        ]);
    }
}
