<?php

namespace Modules\Hostels\Filament\Resources\BookingChangeRequests\Schemas;

use Filament\Infolists;
use Filament\Schemas\Schema;

class BookingChangeRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Infolists\Components\Section::make('Booking Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('booking.booking_reference')
                            ->label('Booking Reference'),
                        Infolists\Components\TextEntry::make('booking.hostelOccupant.full_name')
                            ->label('Hostel Occupant'),
                        Infolists\Components\TextEntry::make('booking.room.room_number')
                            ->label('Current Room'),
                        Infolists\Components\TextEntry::make('booking.bed.bed_number')
                            ->label('Current Bed')
                            ->default('Not assigned'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Change Request Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('requestedRoom.room_number')
                            ->label('Requested Room'),
                        Infolists\Components\TextEntry::make('requestedBed.bed_number')
                            ->label('Requested Bed')
                            ->default('No preference'),
                        Infolists\Components\TextEntry::make('reason'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->colors([
                                'warning' => 'pending',
                                'success' => 'approved',
                                'danger' => 'rejected',
                            ]),
                        Infolists\Components\TextEntry::make('notes'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Approval Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('approvedBy.name')
                            ->label('Approved By')
                            ->default('Not approved yet'),
                        Infolists\Components\TextEntry::make('approved_at')
                            ->dateTime()
                            ->default('Not approved yet'),
                    ])
                    ->columns(2),
            ]);
    }
}
