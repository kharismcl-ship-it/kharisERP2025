<?php

namespace Modules\Finance\Http\Livewire\Invoices;

use Livewire\Component;
use Modules\Finance\App\Models\Invoice;

class Index extends Component
{
    public function render()
    {
        $companyId = app()->has('current_company_id') ? app('current_company_id') : session('current_company_id');

        $invoices = collect();
        if ($companyId) {
            $invoices = Invoice::where('company_id', $companyId)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('finance::livewire.invoices.index', [
            'invoices' => $invoices,
        ])->layout('layouts.app');
    }
}
