<?php

namespace Modules\Finance\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FinanceIndex extends Component
{
    public function render()
    {
        $companyId = app()->has('current_company_id') ? app('current_company_id') : session('current_company_id');

        $accounts = collect();
        if ($companyId) {
            $accounts = DB::table('accounts')
                ->where('company_id', $companyId)
                ->orderBy('code')
                ->get();
        }

        return view('finance::livewire.finance-index', [
            'accounts' => $accounts,
        ])->layout('layouts.app');
    }
}
