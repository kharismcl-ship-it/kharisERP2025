<?php

namespace Modules\ManufacturingWater\Filament\Resources\Staff\MyWaterTestResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WaterTestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Water Test Record')
                ->columns(3)
                ->schema([
                    TextEntry::make('test_date')->date()->weight('bold'),
                    TextEntry::make('test_type')
                        ->label('Type')
                        ->badge()
                        ->formatStateUsing(fn ($state) => ucfirst($state)),
                    IconEntry::make('passed')->label('Pass/Fail')->boolean(),
                    TextEntry::make('plant.name')->label('Plant')->placeholder('—'),
                    TextEntry::make('tested_by')->label('Tested By')->placeholder('—'),
                ]),

            Section::make('Test Results')
                ->columns(3)
                ->schema([
                    TextEntry::make('ph')->label('pH')->placeholder('—'),
                    TextEntry::make('turbidity_ntu')->label('Turbidity (NTU)')->placeholder('—'),
                    TextEntry::make('chlorine_residual')->label('Chlorine Residual')->placeholder('—'),
                    TextEntry::make('temperature')->label('Temperature (°C)')->placeholder('—'),
                    TextEntry::make('dissolved_oxygen')->label('Dissolved Oxygen')->placeholder('—'),
                    TextEntry::make('conductivity')->label('Conductivity')->placeholder('—'),
                    TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
                ]),
        ]);
    }
}
