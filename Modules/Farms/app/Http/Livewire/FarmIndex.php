<?php

namespace Modules\Farms\Http\Livewire;

use Livewire\Component;
use Modules\Farms\Models\Farm;

class FarmIndex extends Component
{
    public function render()
    {
        $companyId = app()->has('current_company_id') ? app('current_company_id') : session('current_company_id');

        $farms = collect();
        if ($companyId) {
            $farms = Farm::where('company_id', $companyId)->orderBy('name')->get();
        }

        return view('farms::livewire.farm-index', [
            'farms' => $farms,
        ])->layout('farms::layouts.app');
    }
}
