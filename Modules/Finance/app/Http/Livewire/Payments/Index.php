<?php

namespace Modules\Finance\Http\Livewire\Payments;

use Livewire\Component;
use Modules\Finance\App\Models\Payment;

class Index extends Component
{
    public function render()
    {
        $companyId = app()->has('current_company_id') ? app('current_company_id') : session('current_company_id');

        $payments = collect();
        if ($companyId) {
            $payments = Payment::where('company_id', $companyId)
                ->with('invoice')
                ->orderBy('payment_date', 'desc')
                ->paginate(10);
        }

        return view('finance::livewire.payments.index', [
            'payments' => $payments,
        ])->layout('layouts.app');
    }
}
