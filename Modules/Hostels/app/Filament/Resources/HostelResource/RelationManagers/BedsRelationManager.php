<?php

namespace Modules\Hostels\Filament\Resources\HostelResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Modules\Hostels\Models\Room;

class BedsRelationManager extends RelationManager
{
    protected static string $relationship = 'beds';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('room_id')
                    ->label('Room No.')
//                    ->relationship('room', 'room_number')
                    ->relationship(
                        name: 'room',
                        titleAttribute: 'room_number',
                        modifyQueryUsing: fn ($query) => $query->where('hostel_id', $this->getOwnerRecord()->id),
                    )
                    ->searchable()
                    ->live()
                    ->preload()
                    ->required(),

                Select::make('bed_number')
                    ->label('Bed No.')
                    ->options(function (callable $get) {
                        $roomId = $get('room_id');
                        if (! $roomId) {
                            return [];
                        }

                        $room = Room::find($roomId);
                        if (! $room || ! $room->max_occupancy) {
                            return [];
                        }

                        $range = range(1, $room->max_occupancy);

                        return array_combine($range, $range);
                    })
                    ->required()
                    ->rules([
                        function (Get $get) {
                            return Rule::unique('beds', 'bed_number')
                                ->where('room_id', $get('room_id'))
                                ->ignore($this->getOwnerRecord()?->id); // allow editing same row
                        },
                    ]),

                Select::make('status')
                    ->options([
                        'available' => 'Available',
                        'reserved' => 'Reserved',
                        'occupied' => 'Occupied',
                        'maintenance' => 'Maintenance',
                        'blocked' => 'Blocked',
                    ])
                    ->required()
                    ->default('available'),
                Toggle::make('is_upper_bunk')
                    ->label('Upper Bunk'),
                Textarea::make('notes')
                    ->rows(3)
                    ->default('This is a bed entry'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('bed_number')
            ->columns([
                TextColumn::make('bed_number'),
                TextColumn::make('room.room_number')
                    ->label('Room'),
                TextColumn::make('room.block.name')
                    ->label('Block'),
                TextColumn::make('room.floor.name')
                    ->label('Floor'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'reserved' => 'warning',
                        'occupied' => 'danger',
                        'maintenance' => 'info',
                        'blocked' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('is_upper_bunk')
                    ->label('Upper Bunk')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
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
