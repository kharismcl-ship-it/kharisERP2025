<?php

namespace Modules\HR\Http\Livewire\OrgChart;

use Livewire\Component;
use Modules\HR\Models\Employee;

class Index extends Component
{
    /** @var \Illuminate\Database\Eloquent\Collection */
    public $roots;

    public function mount(): void
    {
        $companyId = app('current_company_id');

        // Load all active employees with eager-loaded relations
        $all = Employee::where('company_id', $companyId)
            ->where('employment_status', 'active')
            ->with(['jobPosition', 'department', 'subordinates'])
            ->get()
            ->keyBy('id');

        // Roots = employees with no manager (or whose manager is not in this company)
        $this->roots = $all->filter(function (Employee $e) use ($all) {
            return ! $e->reporting_to_employee_id || ! $all->has($e->reporting_to_employee_id);
        })->values();
    }

    public function render()
    {
        return view('hr::livewire.org-chart.index')
            ->layout('hr::layouts.master');
    }
}
