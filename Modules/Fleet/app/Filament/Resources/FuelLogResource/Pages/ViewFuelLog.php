<?php

namespace Modules\Fleet\Filament\Resources\FuelLogResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Fleet\Filament\Resources\FuelLogResource;

class ViewFuelLog extends ViewRecord
{
    protected static string $resource = FuelLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Fuel Fill Overview')
                ->description('Core details of this fuel fill event')
                ->columns(3)
                ->schema([
                    TextEntry::make('vehicle.name')
                        ->label('Vehicle')
                        ->weight('bold')
                        ->icon('heroicon-o-truck'),

                    TextEntry::make('vehicle.plate')
                        ->label('Plate Number')
                        ->badge()
                        ->color('gray'),

                    TextEntry::make('fill_date')
                        ->label('Fill Date')
                        ->date('d M Y'),

                    TextEntry::make('fuel_station')
                        ->label('Fuel Station')
                        ->placeholder('—'),

                    TextEntry::make('driver.name')
                        ->label('Driver')
                        ->placeholder('Unassigned'),

                    TextEntry::make('vehicle.fuel_type')
                        ->label('Fuel Type')
                        ->badge()
                        ->color('primary'),
                ]),

            Section::make('Consumption & Cost')
                ->description('Volume and financial breakdown')
                ->columns(4)
                ->schema([
                    TextEntry::make('litres')
                        ->label('Volume Filled')
                        ->suffix(' L')
                        ->numeric(decimalPlaces: 3)
                        ->weight('bold')
                        ->size(TextEntry\TextEntrySize::Large),

                    TextEntry::make('price_per_litre')
                        ->label('Price / Litre')
                        ->money('GHS'),

                    TextEntry::make('total_cost')
                        ->label('Total Cost')
                        ->money('GHS')
                        ->weight('bold')
                        ->size(TextEntry\TextEntrySize::Large)
                        ->color('warning'),

                    TextEntry::make('mileage_at_fill')
                        ->label('Mileage at Fill')
                        ->numeric(decimalPlaces: 0)
                        ->suffix(' km')
                        ->placeholder('—'),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([
                    TextEntry::make('notes')
                        ->columnSpanFull()
                        ->placeholder('No notes recorded'),
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
