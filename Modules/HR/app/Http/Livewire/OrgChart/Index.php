<?php

namespace Modules\HR\Http\Livewire\OrgChart;

use Livewire\Component;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;

class Index extends Component
{
    public $departments = [];

    public $employees = [];

    public function mount()
    {
        $this->loadOrgChart();
    }

    public function loadOrgChart()
    {
        $companyId = app('current_company_id');

        $this->departments = Department::where('company_id', $companyId)
            ->where('is_active', true)
            ->with(['employees.jobPosition', 'children'])
            ->get();

        // Load all employees with their managers
        $this->employees = Employee::where('company_id', $companyId)
            ->with(['department', 'jobPosition', 'manager'])
            ->get();
    }

    public function render()
    {
        return view('hr::livewire.org-chart.index');
    }
}
