<?php

namespace Modules\Hostels\Filament\Resources\BookingChangeRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class BookingChangeRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking.booking_reference')
                    ->label('Booking')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('booking.hostelOccupant.full_name')
                    ->label('Hostel Occupant')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('booking.room.room_number')
                    ->label('Current Room')
                    ->sortable(),
                Tables\Columns\TextColumn::make('requestedRoom.room_number')
                    ->label('Requested Room')
                    ->sortable(),
                Tables\Columns\TextColumn::make('requestedBed.bed_number')
                    ->label('Requested Bed')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reason')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
