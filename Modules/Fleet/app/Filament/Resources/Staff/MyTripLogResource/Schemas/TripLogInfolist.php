<?php

namespace Modules\Fleet\Filament\Resources\Staff\MyTripLogResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TripLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Trip Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('trip_reference')->label('Reference')->badge()->color('gray'),
                    TextEntry::make('trip_date')->date()->label('Date'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'completed'   => 'success',
                            'in_progress' => 'warning',
                            'planned'     => 'info',
                            'cancelled'   => 'danger',
                            default       => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state))),
                    TextEntry::make('vehicle.registration_number')->label('Vehicle')->placeholder('—'),
                    TextEntry::make('origin'),
                    TextEntry::make('destination'),
                    TextEntry::make('purpose')->columnSpanFull()->placeholder('—'),
                    TextEntry::make('distance_km')->label('Distance (km)')->placeholder('—'),
                    TextEntry::make('start_odometer')->label('Start Odometer')->placeholder('—'),
                    TextEntry::make('end_odometer')->label('End Odometer')->placeholder('—'),
                    TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
                ]),
        ]);
    }
}
