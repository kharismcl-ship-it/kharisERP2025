<?php

namespace Modules\Hostels\Filament\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Modules\Hostels\Filament\Resources\HostelOccupantResource\Pages;
use Modules\Hostels\Filament\Resources\HostelOccupantResource\RelationManagers\BookingsRelationManager;
use Modules\Hostels\Models\HostelOccupant;

class HostelOccupantResource extends Resource
{
    protected static ?string $model = HostelOccupant::class;

    protected static ?string $slug = 'hostel-occupants';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('hostel_id')
                    ->relationship('hostel', 'name')
                    ->searchable()
                    ->required(),

                TextInput::make('first_name')
                    ->required(),

                TextInput::make('last_name')
                    ->required(),

                TextInput::make('other_names'),

                TextInput::make('full_name')
                    ->required(),

                Select::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ])
                    ->required(),

                DatePicker::make('dob')
                    ->label('Date of Birth')
                    ->nullable(),

                TextInput::make('phone')
                    ->tel()
                    ->required(),

                TextInput::make('alt_phone')
                    ->tel()
                    ->nullable(),

                TextInput::make('email')
                    ->email()
                    ->nullable(),

                TextInput::make('national_id_number')
                    ->nullable(),

                TextInput::make('student_id')
                    ->nullable(),

                TextInput::make('institution')
                    ->nullable(),

                TextInput::make('guardian_name')
                    ->nullable(),

                TextInput::make('guardian_phone')
                    ->tel()
                    ->nullable(),

                TextInput::make('guardian_email')
                    ->email()
                    ->nullable(),

                Textarea::make('address')
                    ->nullable(),

                TextInput::make('emergency_contact_name')
                    ->nullable(),

                TextInput::make('emergency_contact_phone')
                    ->tel()
                    ->nullable(),

                Select::make('status')
                    ->options([
                        'prospect' => 'Prospect',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'blacklisted' => 'Blacklisted',
                    ])
                    ->required()
                    ->default('prospect'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hostel.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('first_name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('last_name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('student_id')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('gender')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'prospect' => 'warning',
                        'inactive' => 'gray',
                        'blacklisted' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'active' => 'heroicon-o-check-circle',
                        'prospect' => 'heroicon-o-clock',
                        'inactive' => 'heroicon-o-x-circle',
                        'blacklisted' => 'heroicon-o-shield-exclamation',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->label('Hostel Occupant Status'),

                TextColumn::make('current_booking_status')
                    ->label('Occupancy Status')
                    ->getStateUsing(function (HostelOccupant $record): string {
                        $activeBooking = $record->bookings()
                            ->whereIn('status', ['confirmed', 'awaiting_check_in', 'checked_in'])
                            ->whereNull('actual_check_out_at')
                            ->first();

                        if (! $activeBooking) {
                            return 'Not Occupying';
                        }

                        return match ($activeBooking->status) {
                            'confirmed' => 'Booked (Not Checked In)',
                            'awaiting_check_in' => 'Awaiting Check-in',
                            'checked_in' => 'Currently Occupying',
                            default => 'Not Occupying',
                        };
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Currently Occupying' => 'success',
                        'Booked (Not Checked In)' => 'warning',
                        'Awaiting Check-in' => 'info',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Currently Occupying' => 'heroicon-o-home',
                        'Booked (Not Checked In)' => 'heroicon-o-calendar',
                        'Awaiting Check-in' => 'heroicon-o-clock',
                        default => 'heroicon-o-home',
                    }),

                TextColumn::make('bed_info')
                    ->label('Current Bed')
                    ->getStateUsing(function (HostelOccupant $record): ?string {
                        $activeBooking = $record->bookings()
                            ->whereIn('status', ['confirmed', 'awaiting_check_in', 'checked_in'])
                            ->whereNull('actual_check_out_at')
                            ->with(['bed.room'])
                            ->first();

                        if (! $activeBooking || ! $activeBooking->bed) {
                            return null;
                        }

                        return $activeBooking->bed->room->room_number.' - Bed '.$activeBooking->bed->bed_number;
                    })
                    ->placeholder('No active booking'),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'prospect' => 'Prospect',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'blacklisted' => 'Blacklisted',
                    ])
                    ->label('Hostel Occupant Status'),

                \Filament\Tables\Filters\SelectFilter::make('occupancy_status')
                    ->query(function ($query, $data) {
                        if (! empty($data['value'])) {
                            $statusMap = [
                                'occupying' => ['checked_in'],
                                'booked_not_checked_in' => ['confirmed', 'awaiting_check_in'],
                                'not_occupying' => ['cancelled', 'checked_out', 'no_show'],
                            ];

                            if (isset($statusMap[$data['value']])) {
                                $query->whereHas('bookings', function ($q) use ($statusMap, $data) {
                                    $q->whereIn('status', $statusMap[$data['value']])
                                        ->whereNull('actual_check_out_at');
                                });
                            }
                        }
                    })
                    ->options([
                        'occupying' => 'Currently Occupying',
                        'booked_not_checked_in' => 'Booked (Not Checked In)',
                        'not_occupying' => 'Not Occupying',
                    ])
                    ->label('Occupancy Status'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                Action::make('checkIn')
                    ->label('Check In')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Check In Hostel Occupant')
                    ->modalDescription('Mark this hostel occupant as checked in and update bed status to occupied.')
                    ->form([
                        DateTimePicker::make('actual_check_in_at')
                            ->label('Actual Check-in Time')
                            ->default(now())
                            ->required(),
                        TextInput::make('check_in_notes')
                            ->label('Check-in Notes')
                            ->placeholder('Any special notes for check-in')
                            ->nullable(),
                    ])
                    ->action(function (HostelOccupant $record, array $data) {
                        try {
                            // Get the occupant's active booking
                            $booking = $record->bookings()
                                ->whereIn('status', ['confirmed', 'awaiting_check_in'])
                                ->whereNull('actual_check_in_at')
                                ->first();

                            if (! $booking) {
                                throw new \Exception('No active booking found for check-in');
                            }

                            // Update booking
                            $booking->update([
                                'actual_check_in_at' => $data['actual_check_in_at'],
                                'status' => 'checked_in',
                                'notes' => ($booking->notes ? $booking->notes.'\n' : '').
                                          'Checked in: '.($data['check_in_notes'] ?? 'No notes'),
                            ]);

                            // Update bed status to occupied
                            if ($booking->bed) {
                                $booking->bed->update(['status' => 'occupied']);
                            }

                            // Update occupant status to active
                            $record->update(['status' => 'active']);

                            Notification::make()
                                ->title('Hostel Occupant Checked In Successfully')
                                ->success()
                                ->send();

                            Log::info('hostels.occupant.checked_in', [
                                'occupant_id' => $record->id,
                                'booking_id' => $booking->id,
                                'bed_id' => $booking->bed_id,
                                'check_in_time' => $data['actual_check_in_at'],
                            ]);

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Check-in Failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();

                            throw $e;
                        }
                    })
                    ->visible(fn (HostelOccupant $record): bool => $record->status !== 'active' &&
                        $record->bookings()->whereIn('status', ['confirmed', 'awaiting_check_in'])->exists()
                    ),
                Action::make('checkOut')
                    ->label('Check Out')
                    ->icon('heroicon-o-arrow-left-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Check Out Hostel Occupant')
                    ->modalDescription('Mark this hostel occupant as checked out and release the bed for new bookings.')
                    ->form([
                        DateTimePicker::make('actual_check_out_at')
                            ->label('Actual Check-out Time')
                            ->default(now())
                            ->required(),
                        TextInput::make('check_out_notes')
                            ->label('Check-out Notes')
                            ->placeholder('Any special notes for check-out')
                            ->nullable(),
                        TextInput::make('deposit_refund_amount')
                            ->label('Deposit Refund Amount')
                            ->numeric()
                            ->placeholder('0.00')
                            ->default(0)
                            ->nullable(),
                    ])
                    ->action(function (HostelOccupant $record, array $data) {
                        try {
                            // Get the occupant's active checked-in booking
                            $booking = $record->bookings()
                                ->where('status', 'checked_in')
                                ->whereNotNull('actual_check_in_at')
                                ->whereNull('actual_check_out_at')
                                ->first();

                            if (! $booking) {
                                throw new \Exception('No active checked-in booking found for check-out');
                            }

                            // Update booking
                            $booking->update([
                                'actual_check_out_at' => $data['actual_check_out_at'],
                                'status' => 'checked_out',
                                'deposit_refunded' => $data['deposit_refund_amount'] ?? 0,
                                'refund_processed_at' => now(),
                                'notes' => ($booking->notes ? $booking->notes.'\n' : '').
                                          'Checked out: '.($data['check_out_notes'] ?? 'No notes').
                                          ($data['deposit_refund_amount'] ?
                                          ' | Deposit refund: GHS '.number_format($data['deposit_refund_amount'], 2) : ''),
                            ]);

                            // Release bed and set back to available
                            if ($booking->bed) {
                                $booking->bed->update(['status' => 'available']);
                            }

                            // Update occupant status to inactive
                            $record->update(['status' => 'inactive']);

                            Notification::make()
                                ->title('Hostel Occupant Checked Out Successfully')
                                ->success()
                                ->send();

                            Log::info('hostels.occupant.checked_out', [
                                'occupant_id' => $record->id,
                                'booking_id' => $booking->id,
                                'bed_id' => $booking->bed_id,
                                'check_out_time' => $data['actual_check_out_at'],
                                'deposit_refunded' => $data['deposit_refund_amount'] ?? 0,
                            ]);

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Check-out Failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();

                            throw $e;
                        }
                    })
                    ->visible(fn (HostelOccupant $record): bool => $record->status === 'active' &&
                        $record->bookings()->where('status', 'checked_in')->exists()
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            BookingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHostelOccupants::route('/'),
            'create' => Pages\CreateHostelOccupant::route('/create'),
            'edit' => Pages\EditHostelOccupant::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['company']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'company.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->company) {
            $details['Company'] = $record->company->name;
        }

        return $details;
    }
}
