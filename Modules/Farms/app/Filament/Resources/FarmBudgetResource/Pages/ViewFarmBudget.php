<?php

namespace Modules\Farms\Filament\Resources\FarmBudgetResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Farms\Filament\Resources\FarmBudgetResource;

class ViewFarmBudget extends ViewRecord
{
    protected static string $resource = FarmBudgetResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make(), DeleteAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Budget Overview')
                ->columns(3)
                ->schema([
                    TextEntry::make('budget_name'),
                    TextEntry::make('farm.name')->label('Farm'),
                    TextEntry::make('category')->badge()->color('primary'),

                    TextEntry::make('budget_year')->label('Year'),
                    TextEntry::make('budget_month')
                        ->label('Month')
                        ->formatStateUsing(fn ($state) => $state
                            ? \Carbon\Carbon::create()->month($state)->format('F')
                            : 'Full Year'
                        ),
                    TextEntry::make('cropCycle.crop_name')->label('Crop Cycle')->placeholder('—'),
                ]),

            Section::make('Budget vs Actual')
                ->columns(3)
                ->schema([
                    TextEntry::make('budgeted_amount')->money('GHS')->label('Budgeted'),
                    TextEntry::make('actual_amount')->money('GHS')->label('Actual'),
                    TextEntry::make('variance_display')
                        ->label('Variance')
                        ->getStateUsing(fn ($record) => 'GHS ' . number_format($record->variance, 2))
                        ->color(fn ($record) => $record->variance > 0 ? 'danger' : 'success'),
                    TextEntry::make('variance_pct_display')
                        ->label('Variance %')
                        ->getStateUsing(fn ($record) =>
                            $record->variance_pct !== null ? $record->variance_pct . '%' : '—'
                        )
                        ->color(fn ($record) => ($record->variance_pct ?? 0) > 0 ? 'danger' : 'success'),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([
                    TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
                ]),

            Section::make('Audit')
                ->collapsible()
                ->collapsed()
                ->columns(2)
                ->schema([
                    TextEntry::make('created_at')->dateTime('d M Y H:i'),
                    TextEntry::make('updated_at')->dateTime('d M Y H:i'),
                ]),
        ]);
    }
}