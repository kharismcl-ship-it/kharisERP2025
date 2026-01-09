<?php

namespace Modules\Hostels\Filament\Resources\BookingChangeRequests\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;

class BookingChangeRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('booking_id')
                    ->relationship('booking', 'booking_reference')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('requested_room_id')
                    ->relationship('requestedRoom', 'room_number')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('requested_bed_id')
                    ->relationship('requestedBed', 'bed_number')
                    ->searchable()
                    ->preload(),
                Forms\Components\Textarea::make('reason')
                    ->required()
                    ->maxLength(500),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->maxLength(500),
            ]);
    }
}
