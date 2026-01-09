<?php

namespace Modules\ManufacturingWater\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ManufacturingWaterIndex extends Component
{
    public function render()
    {
        $companyId = app()->has('current_company_id') ? app('current_company_id') : session('current_company_id');

        $plants = collect();
        if ($companyId) {
            $plants = DB::table('mw_plants')
                ->where('company_id', $companyId)
                ->orderBy('name')
                ->get();
        }

        return view('manufacturingwater::livewire.manufacturing-water-index', [
            'plants' => $plants,
        ])->layout('layouts.app');
    }
}
