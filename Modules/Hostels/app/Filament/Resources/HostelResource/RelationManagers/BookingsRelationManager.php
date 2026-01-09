<?php

namespace Modules\Hostels\Filament\Resources\HostelResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BookingsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookings';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('booking_reference')
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('hostel_occupant_id')
                    ->relationship('hostelOccupant', 'full_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('room_id')
                    ->relationship('room', 'room_number')
                    ->searchable()
                    ->preload(),
                Select::make('bed_id')
                    ->relationship('bed', 'bed_number')
                    ->searchable()
                    ->preload(),
                Select::make('booking_type')
                    ->options([
                        'academic' => 'Academic',
                        'short_stay' => 'Short Stay',
                    ])
                    ->required(),
                TextInput::make('academic_year'),
                TextInput::make('semester'),
                DatePicker::make('check_in_date')
                    ->required(),
                DatePicker::make('check_out_date')
                    ->required(),
                DatePicker::make('actual_check_in_at')
                    ->label('Actual Check-in Time'),
                DatePicker::make('actual_check_out_at')
                    ->label('Actual Check-out Time'),
                Select::make('status')
                    ->options([
                        'pending_approval' => 'Pending Approval',
                        'pending' => 'Pending',
                        'awaiting_payment' => 'Awaiting Payment',
                        'confirmed' => 'Confirmed',
                        'checked_in' => 'Checked In',
                        'checked_out' => 'Checked Out',
                        'no_show' => 'No Show',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->default('pending_approval'),
                TextInput::make('total_amount')
                    ->numeric()
                    ->required(),
                TextInput::make('amount_paid')
                    ->numeric(),
                TextInput::make('balance_amount')
                    ->numeric(),
                Select::make('payment_status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'partially_paid' => 'Partially Paid',
                        'paid' => 'Paid',
                        'overpaid' => 'Overpaid',
                    ]),
                Select::make('channel')
                    ->options([
                        'walk_in' => 'Walk In',
                        'online' => 'Online',
                        'agent' => 'Agent',
                    ]),
                Textarea::make('notes')
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('booking_reference')
            ->columns([
                TextColumn::make('booking_reference')
                    ->label('Reference'),
                TextColumn::make('hostelOccupant.full_name')
                    ->label('Hostel Occupant'),
                TextColumn::make('room.room_number')
                    ->label('Room'),
                TextColumn::make('bed.bed_number')
                    ->label('Bed'),
                TextColumn::make('booking_type'),
                TextColumn::make('check_in_date')
                    ->date(),
                TextColumn::make('check_out_date')
                    ->date(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending_approval' => 'warning',
                        'pending' => 'info',
                        'awaiting_payment' => 'warning',
                        'confirmed' => 'success',
                        'checked_in' => 'success',
                        'checked_out' => 'gray',
                        'no_show' => 'danger',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('total_amount')
                    ->money('GHS'),
                TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unpaid' => 'danger',
                        'partially_paid' => 'warning',
                        'paid' => 'success',
                        'overpaid' => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
