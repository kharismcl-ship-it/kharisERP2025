<?php

namespace Modules\Finance\Http\Livewire\Reports;

use Livewire\Component;

class FinancialReports extends Component
{
    public function render()
    {
        return view('finance::livewire.reports.financial-reports')->layout('layouts.app');
    }
}
