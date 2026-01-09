<?php

namespace Modules\Finance\Http\Livewire\Accounts;

use Livewire\Component;
use Modules\Finance\App\Models\Account;

class ChartOfAccounts extends Component
{
    public function render()
    {
        $companyId = app()->has('current_company_id') ? app('current_company_id') : session('current_company_id');

        $accounts = collect();
        if ($companyId) {
            $accounts = Account::where('company_id', $companyId)
                ->orderBy('code')
                ->get();
        }

        return view('finance::livewire.accounts.chart-of-accounts', [
            'accounts' => $accounts,
        ])->layout('layouts.app');
    }
}
