<?php

namespace Modules\Farms\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FarmIndex extends Component
{
    public function render()
    {
        $companyId = app()->has('current_company_id') ? app('current_company_id') : session('current_company_id');

        $farms = collect();
        if ($companyId) {
            $farms = DB::table('farms')
                ->where('company_id', $companyId)
                ->orderBy('name')
                ->get();
        }

        return view('farms::livewire.farm-index', [
            'farms' => $farms,
        ])->layout('layouts.app');
    }
}
