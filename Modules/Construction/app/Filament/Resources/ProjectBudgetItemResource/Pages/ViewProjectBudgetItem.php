<?php

namespace Modules\Construction\Filament\Resources\ProjectBudgetItemResource\Pages;

use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\ViewRecord;
use Modules\Construction\Filament\Resources\ProjectBudgetItemResource;
use Modules\Construction\Models\ProjectBudgetItem;

class ViewProjectBudgetItem extends ViewRecord
{
    protected static string $resource = ProjectBudgetItemResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Budget Item')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('project.name')->label('Project'),
                    TextEntry::make('category')
                        ->badge()
                        ->color('info')
                        ->formatStateUsing(fn ($state) => ucfirst($state)),
                ]),
                TextEntry::make('description')->columnSpanFull()->placeholder('—'),
            ]),

            Section::make('Financials')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('budgeted_amount')->label('Budgeted')->money('GHS'),
                    TextEntry::make('actual_amount')->label('Actual')->money('GHS'),
                    TextEntry::make('variance')
                        ->label('Variance')
                        ->getStateUsing(fn (ProjectBudgetItem $record) => $record->variance)
                        ->formatStateUsing(fn ($state) => 'GHS ' . number_format((float) $state, 2))
                        ->color(fn (ProjectBudgetItem $record): string => match (true) {
                            $record->variance > 0 => 'success',
                            $record->variance < 0 => 'danger',
                            default               => 'gray',
                        }),
                ]),
            ]),

            Section::make('Notes')->schema([
                TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }
}
