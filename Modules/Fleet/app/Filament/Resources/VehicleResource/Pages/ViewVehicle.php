<?php

namespace Modules\Fleet\Filament\Resources\VehicleResource\Pages;

use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\ViewRecord;
use Modules\Fleet\Filament\Resources\VehicleResource;

class ViewVehicle extends ViewRecord
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Vehicle Identity')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('name'),
                    TextEntry::make('plate')->label('Plate Number'),
                    TextEntry::make('status')->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'active'            => 'success',
                            'inactive'          => 'gray',
                            'under_maintenance' => 'warning',
                            'retired'           => 'danger',
                            default             => 'gray',
                        }),
                ]),
                Grid::make(4)->schema([
                    TextEntry::make('make'),
                    TextEntry::make('model'),
                    TextEntry::make('year'),
                    TextEntry::make('type')->badge(),
                ]),
                Grid::make(4)->schema([
                    TextEntry::make('color'),
                    TextEntry::make('fuel_type')->label('Fuel Type')->badge(),
                    TextEntry::make('capacity')->label('Capacity'),
                    TextEntry::make('current_mileage')->label('Current Mileage (km)')->numeric(),
                ]),
            ]),
            Section::make('Technical Details')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('chassis_number')->label('Chassis Number'),
                    TextEntry::make('engine_number')->label('Engine Number'),
                ]),
            ]),
            Section::make('Purchase Info')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('purchase_date')->label('Purchase Date')->date(),
                    TextEntry::make('purchase_price')->label('Purchase Price')->money('GHS'),
                ]),
                TextEntry::make('description'),
            ]),
        ]);
    }
}
