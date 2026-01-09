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
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Hostels\Filament\Resources\RoomResource\Pages;
use Modules\Hostels\Filament\Resources\RoomResource\RelationManagers\BedsRelationManager;
use Modules\Hostels\Models\Room;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $slug = 'rooms';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        Select::make('hostel_id')
                            ->relationship('hostel', 'name')
                            ->searchable()
                            ->required(),

                        Select::make('block_id')
                            ->relationship('block', 'name')
                            ->preload()
                            ->searchable()
                            ->nullable(),

                        Select::make('floor_id')
                            ->relationship('floor', 'name')
                            ->preload()
                            ->searchable()
                            ->nullable(),

                        TextInput::make('room_number')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Room Configuration')
                    ->schema([
                        Select::make('type')
                            ->options([
                                'single' => 'Single',
                                'double' => 'Double',
                                'triple' => 'Triple',
                                'quad' => 'Quad',
                                'dorm' => 'Dorm',
                            ])
                            ->required(),

                        Select::make('gender_policy')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'mixed' => 'Mixed',
                                'inherit_hostel' => 'Inherit Hostel',
                            ])
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Occupancy Information')
                    ->schema([
                        TextInput::make('max_occupancy')
                            ->numeric()
                            ->required(),

                        TextInput::make('current_occupancy')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),

                Section::make('Pricing Information')
                    ->schema([
                        Select::make('billing_cycle')
                            ->options([
                                'per_night' => 'Per Night',
                                'per_semester' => 'Per Semester',
                                'per_year' => 'Per Year',
                            ])
                            ->required(),

                        TextInput::make('base_rate')
                            ->numeric()
                            ->prefix('GHS')
                            ->helperText('Fallback rate used when specific billing cycle rates are not set'),

                        // Show all rate fields regardless of billing cycle selection
                        TextInput::make('per_night_rate')
                            ->label('Per Night Rate')
                            ->numeric()
                            ->prefix('GHS'),

                        TextInput::make('per_semester_rate')
                            ->label('Per Semester Rate')
                            ->numeric()
                            ->prefix('GHS'),

                        TextInput::make('per_year_rate')
                            ->label('Per Year Rate')
                            ->numeric()
                            ->prefix('GHS'),
                    ])
                    ->columns(2),

                Section::make('Status and Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->nullable()
                            ->columnSpanFull(),

                        Select::make('status')
                            ->options([
                                'available' => 'Available',
                                'partially_occupied' => 'Partially Occupied',
                                'full' => 'Full',
                                'maintenance' => 'Maintenance',
                                'closed' => 'Closed',
                            ])
                            ->required()
                            ->default('available'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hostel.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('room_number')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('block.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('floor.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('room_type')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('gender_policy')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('base_rate')
                    ->money('GHS')
                    ->sortable(),

                TextColumn::make('billing_cycle')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('max_occupancy')
                    ->sortable(),

                TextColumn::make('current_occupancy')
                    ->sortable(),

                TextColumn::make('status')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('beds_count')
                    ->label('Beds')
                    ->counts('beds')
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
                                    'partially_occupied' => 'Partially Occupied',
                                    'full' => 'Full',
                                    'maintenance' => 'Maintenance',
                                    'closed' => 'Closed',
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
            BedsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['hostel']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['hostel.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->hostel) {
            $details['Hostel'] = $record->hostel->name;
        }

        return $details;
    }
}
