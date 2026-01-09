<?php

namespace Modules\ManufacturingPaper\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ManufacturingPaperIndex extends Component
{
    public function render()
    {
        $companyId = app()->has('current_company_id') ? app('current_company_id') : session('current_company_id');

        $plants = collect();
        if ($companyId) {
            $plants = DB::table('mp_plants')
                ->where('company_id', $companyId)
                ->orderBy('name')
                ->get();
        }

        return view('manufacturingpaper::livewire.manufacturing-paper-index', [
            'plants' => $plants,
        ])->layout('layouts.app');
    }
}
