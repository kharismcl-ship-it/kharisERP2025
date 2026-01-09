<?php

namespace Modules\HR\Http\Livewire\EmployeeSalaries;

use Livewire\Component;
use Modules\HR\Models\EmployeeSalary;

class Index extends Component
{
    public function render()
    {
        $salaries = EmployeeSalary::with(['employee', 'company', 'salaryScale'])
            ->latest()
            ->paginate(10);

        return view('hr::livewire.employee-salaries.index', compact('salaries'))
            ->layout('hr::layouts.master');
    }
}
