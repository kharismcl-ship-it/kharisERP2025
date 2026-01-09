<?php

namespace Modules\Construction\Http\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ConstructionIndex extends Component
{
    public function render()
    {
        $companyId = app()->has('current_company_id') ? app('current_company_id') : session('current_company_id');

        $projects = collect();
        if ($companyId) {
            $projects = DB::table('construction_projects')
                ->where('company_id', $companyId)
                ->orderBy('name')
                ->get();
        }

        return view('construction::livewire.construction-index', [
            'projects' => $projects,
        ])->layout('layouts.app');
    }
}
