<?php

namespace Modules\Sales\Filament\Pages;

use Filament\Pages\Page;
use Livewire\Attributes\On;
use Modules\Sales\Models\SalesOpportunity;

class SalesOpportunityKanban extends Page
{
    protected string $view = 'sales::filament.pages.sales-opportunity-kanban';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-view-columns';

    protected static string|\UnitEnum|null $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Pipeline Board';

    public function getColumns(): array
    {
        return SalesOpportunity::STAGES;
    }

    public function getOpportunitiesByStage(string $stage): \Illuminate\Database\Eloquent\Collection
    {
        return SalesOpportunity::query()
            ->with('contact')
            ->where('stage', $stage)
            ->orderByDesc('estimated_value')
            ->get();
    }

    #[On('opportunity-stage-changed')]
    public function handleStageChanged(int $opportunityId, string $newStage): void
    {
        SalesOpportunity::findOrFail($opportunityId)->update(['stage' => $newStage]);
    }
}
