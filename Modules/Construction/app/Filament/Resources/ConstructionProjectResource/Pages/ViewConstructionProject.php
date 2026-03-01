<?php

namespace Modules\Construction\Filament\Resources\ConstructionProjectResource\Pages;

use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\ViewRecord;
use Modules\Construction\Filament\Resources\ConstructionProjectResource;

class ViewConstructionProject extends ViewRecord
{
    protected static string $resource = ConstructionProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Project Details')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('name'),
                    TextEntry::make('location'),
                    TextEntry::make('status')->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'active', 'completed' => 'success',
                            'planning'  => 'info',
                            'on_hold'   => 'warning',
                            'cancelled' => 'danger',
                            default     => 'gray',
                        }),
                ]),
                Grid::make(3)->schema([
                    TextEntry::make('client_name')->label('Client'),
                    TextEntry::make('client_contact')->label('Client Contact'),
                    TextEntry::make('project_manager')->label('Project Manager'),
                ]),
                TextEntry::make('description')->columnSpanFull(),
            ]),
            Section::make('Timeline & Financials')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('start_date')->date(),
                    TextEntry::make('expected_end_date')->label('Expected End')->date(),
                    TextEntry::make('actual_end_date')->label('Actual End')->date(),
                ]),
                Grid::make(3)->schema([
                    TextEntry::make('contract_value')->label('Contract Value')->money('GHS'),
                    TextEntry::make('budget')->money('GHS'),
                    TextEntry::make('total_spent')->label('Total Spent')->money('GHS'),
                ]),
            ]),
        ]);
    }
}
