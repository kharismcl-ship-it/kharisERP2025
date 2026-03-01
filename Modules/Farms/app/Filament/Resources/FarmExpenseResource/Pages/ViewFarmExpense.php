<?php

namespace Modules\Farms\Filament\Resources\FarmExpenseResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Farms\Filament\Resources\FarmExpenseResource;

class ViewFarmExpense extends ViewRecord
{
    protected static string $resource = FarmExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Expense Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('farm.name')
                        ->label('Farm')
                        ->weight('bold'),

                    TextEntry::make('expense_date')
                        ->date('d M Y')
                        ->label('Date'),

                    TextEntry::make('category')
                        ->badge()
                        ->color('primary'),

                    TextEntry::make('amount')
                        ->money('GHS')
                        ->weight('bold')
                        ->color('warning'),

                    TextEntry::make('supplier')
                        ->placeholder('—'),

                    TextEntry::make('cropCycle.crop_name')
                        ->label('Crop Cycle')
                        ->placeholder('—'),
                ]),

            Section::make('Description')
                ->schema([
                    TextEntry::make('description')->columnSpanFull(),
                    TextEntry::make('notes')->columnSpanFull()->placeholder('No notes'),
                ]),

            Section::make('Audit')
                ->columns(2)
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('created_at')->dateTime()->label('Created'),
                    TextEntry::make('updated_at')->dateTime()->label('Last Updated'),
                ]),
        ]);
    }
}
