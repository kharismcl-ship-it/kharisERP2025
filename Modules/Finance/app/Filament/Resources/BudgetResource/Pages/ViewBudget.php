<?php

namespace Modules\Finance\Filament\Resources\BudgetResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Finance\Filament\Resources\BudgetResource;

class ViewBudget extends ViewRecord
{
    protected static string $resource = BudgetResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Budget Info')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')->weight('bold'),
                        TextEntry::make('budget_year'),
                        TextEntry::make('period_type')->badge()->color('info'),
                        TextEntry::make('status')->badge()
                            ->color(fn (string $state) => match ($state) {
                                'draft'    => 'gray',
                                'approved' => 'info',
                                'active'   => 'success',
                                'closed'   => 'danger',
                                default    => 'gray',
                            }),
                        TextEntry::make('total_budget')->money('GHS')->weight('bold'),
                        TextEntry::make('notes')->placeholder('—')->columnSpanFull(),
                    ]),
            ]);
    }
}