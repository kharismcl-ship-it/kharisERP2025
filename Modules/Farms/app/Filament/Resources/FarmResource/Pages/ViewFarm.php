<?php

namespace Modules\Farms\Filament\Resources\FarmResource\Pages;

use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\FarmResource;

class ViewFarm extends ViewRecord
{
    protected static string $resource = FarmResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Farm Details')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('name'),
                    TextEntry::make('type')->badge(),
                    TextEntry::make('status')->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'active'   => 'success',
                            'inactive' => 'gray',
                            'fallow'   => 'warning',
                            default    => 'gray',
                        }),
                ]),
                Grid::make(3)->schema([
                    TextEntry::make('location'),
                    TextEntry::make('total_area')->label('Total Area'),
                    TextEntry::make('area_unit')->label('Unit'),
                ]),
                Grid::make(2)->schema([
                    TextEntry::make('owner_name')->label('Owner'),
                    TextEntry::make('owner_phone')->label('Owner Phone'),
                ]),
                TextEntry::make('description')->columnSpanFull(),
            ]),
        ]);
    }
}
