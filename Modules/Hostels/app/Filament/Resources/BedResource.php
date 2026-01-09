<?php

namespace Modules\Hostels\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Hostels\Filament\Resources\BedResource\Pages;
use Modules\Hostels\Filament\Resources\BedResource\RelationManagers\BookingsRelationManager;
use Modules\Hostels\Models\Bed;

class BedResource extends Resource
{
    protected static ?string $model = Bed::class;

    protected static ?string $slug = 'beds';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('room_id')
                    ->label('Room No.')
                    ->relationship('room', 'room_number')
                    ->searchable()
                    ->required(),

                TextInput::make('bed_number')
                    ->label('Bed No.')
                    ->required(),

                ToggleButtons::make('is_upper_bunk')
                    ->required()
                    ->options([
                        1 => 'Yes',
                        0 => 'No',
                    ])
                    ->label('Upper Bunk')
                    ->formatStateUsing(fn ($state): int => (int) $state),

                Textarea::make('notes')
                    ->nullable()
                    ->columnSpanFull(),

                Select::make('status')
                    ->options([
                        'available' => 'Available',
                        'reserved_pending_approval' => 'Reserved (Pending Approval)',
                        'reserved' => 'Reserved (Confirmed)',
                        'occupied' => 'Occupied',
                        'maintenance' => 'Maintenance',
                        'blocked' => 'Blocked',
                    ])
                    ->required()
                    ->default('available')
                    ->helperText('Status workflow: Available → Reserved → Occupied → Available'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('room.room_number')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('bed_number')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('bookings_count')
                    ->label('Bookings')
                    ->counts('bookings')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('setStatus')
                        ->label('Set Status')
                        ->form([
                            Select::make('status')
                                ->options([
                                    'available' => 'Available',
                                    'reserved' => 'Reserved',
                                    'occupied' => 'Occupied',
                                    'maintenance' => 'Maintenance',
                                    'blocked' => 'Blocked',
                                ])
                                ->required(),
                        ])
                        ->action(function (array $records, array $data): void {
                            foreach ($records as $record) {
                                $record->update(['status' => $data['status']]);
                            }
                        }),
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
            'index' => Pages\ListBeds::route('/'),
            'create' => Pages\CreateBed::route('/create'),
            'edit' => Pages\EditBed::route('/{record}/edit'),
        ];
    }
}
