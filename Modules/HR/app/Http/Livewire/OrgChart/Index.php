<?php

namespace Modules\HR\Http\Livewire\OrgChart;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Modules\HR\Models\Employee;

class Index extends Component
{
    /** @var Collection All active employees (passed to recursive partial) */
    public Collection $allEmployees;

    /** @var Collection Root employees (no reporting_to_employee_id in this company) */
    public Collection $rootEmployees;

    public function mount(): void
    {
        $companyId = app('current_company_id');

        // Load all active employees with eager-loaded relations
        $this->allEmployees = Employee::where('company_id', $companyId)
            ->where('employment_status', 'active')
            ->with(['jobPosition', 'department'])
            ->get()
            ->keyBy('id');

        // Roots = employees with no manager (or whose manager is not in this company)
        $this->rootEmployees = $this->allEmployees->filter(function (Employee $e) {
            return ! $e->reporting_to_employee_id || ! $this->allEmployees->has($e->reporting_to_employee_id);
        })->values();
    }

    public function render()
    {
        return view('hr::livewire.org-chart.index')
            ->layout('hr::layouts.master');
    }
}