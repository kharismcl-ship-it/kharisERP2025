<?php

namespace Modules\Fleet\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FleetIndex extends Component
{
    public function render()
    {
        $companyId = app()->has('current_company_id') ? app('current_company_id') : session('current_company_id');

        $vehicles = collect();
        if ($companyId) {
            $vehicles = DB::table('vehicles')
                ->where('company_id', $companyId)
                ->orderBy('name')
                ->get();
        }

        return view('fleet::livewire.fleet-index', [
            'vehicles' => $vehicles,
        ])->layout('layouts.app');
    }
}
