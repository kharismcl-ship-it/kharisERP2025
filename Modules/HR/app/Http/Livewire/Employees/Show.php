<?php

namespace Modules\HR\Http\Livewire\Employees;

use Livewire\Component;
use Modules\HR\Models\Employee;

class Show extends Component
{
    public Employee $employee;

    public $activeTab = 'profile';

    public function mount($employee)
    {
        $this->employee = Employee::findOrFail($employee);
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('hr::livewire.employees.show');
    }
}
