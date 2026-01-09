<?php

namespace Modules\ProcurementInventory\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProcurementInventoryIndex extends Component
{
    public function render()
    {
        $companyId = app()->has('current_company_id') ? app('current_company_id') : session('current_company_id');

        $items = collect();
        if ($companyId) {
            $items = DB::table('items')
                ->where('company_id', $companyId)
                ->orderBy('name')
                ->get();
        }

        return view('procurementinventory::livewire.procurement-inventory-index', [
            'items' => $items,
        ])->layout('layouts.app');
    }
}
