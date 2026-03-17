<?php

namespace Modules\Sales\Filament\Pages;

use App\Filament\Pages\KanbanPage;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Illuminate\Database\Eloquent\Collection;
use Modules\Sales\Models\SalesOpportunity;

class SalesOpportunityKanban extends KanbanPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-view-columns';

    protected static string|\UnitEnum|null $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Pipeline Board';

    use HasPageShield;

    public function getKanbanStatuses(): array
    {
        return [
            ['key' => 'prospecting',  'label' => 'Prospecting',  'border_class' => 'border-gray-400',   'dot_color' => 'bg-gray-400'],
            ['key' => 'qualification','label' => 'Qualification','border_class' => 'border-blue-400',   'dot_color' => 'bg-blue-400'],
            ['key' => 'proposal',     'label' => 'Proposal',     'border_class' => 'border-indigo-400', 'dot_color' => 'bg-indigo-400'],
            ['key' => 'negotiation',  'label' => 'Negotiation',  'border_class' => 'border-yellow-400', 'dot_color' => 'bg-yellow-400'],
            ['key' => 'closed_won',   'label' => 'Closed Won',   'border_class' => 'border-green-500',  'dot_color' => 'bg-green-500'],
            ['key' => 'closed_lost',  'label' => 'Closed Lost',  'border_class' => 'border-red-400',    'dot_color' => 'bg-red-400'],
        ];
    }

    public function getKanbanRecords(string $status): Collection
    {
        return SalesOpportunity::query()
            ->with('contact')
            ->where('stage', $status)
            ->when($this->search, fn ($q) =>
                $q->where('title', 'like', "%{$this->search}%")
            )
            ->orderByDesc('estimated_value')
            ->get();
    }

    protected function onCardMoved(int|string $recordId, string $newStatus): void
    {
        if (! in_array($newStatus, SalesOpportunity::STAGES, true)) {
            return;
        }

        SalesOpportunity::findOrFail($recordId)->update(['stage' => $newStatus]);
    }

    public function getCardView(): string
    {
        return 'sales::filament.kanban.opportunity-card';
    }
}
