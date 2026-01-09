<?php

namespace Modules\HR\Http\Livewire\EmployeeSalaries;

use Livewire\Component;
use Modules\HR\Models\EmployeeSalary;

class Show extends Component
{
    public EmployeeSalary $salary;

    public function mount(EmployeeSalary $salary)
    {
        $this->salary = $salary;
    }

    public function render()
    {
        return view('hr::livewire.employee-salaries.show')
            ->layout('hr::layouts.master');
    }
}
