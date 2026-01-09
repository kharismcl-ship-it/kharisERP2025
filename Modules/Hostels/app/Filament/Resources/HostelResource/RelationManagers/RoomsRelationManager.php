<?php

namespace Modules\Hostels\Filament\Resources\HostelResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;
use Modules\Hostels\Models\Room;

class RoomsRelationManager extends RelationManager
{
    protected static string $relationship = 'rooms';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('room_number')
                            ->label('Room No.')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1000)
                            ->maxLength(255)
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule) => $rule->where(
                                    'hostel_id',
                                    $this->getOwnerRecord()->id,
                                ),
                            ),

                        Select::make('block_id')
                            ->relationship(
                                name: 'block',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query->where('hostel_id', $this->getOwnerRecord()->id),
                            )
                            ->searchable()
                            ->preload()
                            ->live(),

                        Select::make('floor_id')
                            ->label('Floor')
                            ->relationship(
                                name: 'floor',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                    ->where('hostel_id', $this->getOwnerRecord()->id)
                                    ->when(
                                        $get('block_id'),
                                        fn (Builder $q, $blockId) => $q->where('hostel_block_id', $blockId),
                                    ),
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn (Get $get) => blank($get('block_id')))
                            ->live(),
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
                            ->label('Room Type')
                            ->disabled(fn (Get $get) => blank($get('floor_id')))
                            ->live()
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
                            ->required()
                            ->reactive() // listens for other field changes
                            ->afterStateHydrated(function (callable $set, $state, Get $get) {
                                // Set initial value
                                if (! $state) {
                                    $set('max_occupancy', match ($get('type')) {
                                        'single' => 1,
                                        'double' => 2,
                                        'triple' => 3,
                                        'quad' => 4,
                                        'dorm' => 8,
                                        default => 0,
                                    });
                                }
                            })
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                // Update when type changes
                                $set('max_occupancy', match ($get('type')) {
                                    'single' => 1,
                                    'double' => 2,
                                    'triple' => 3,
                                    'quad' => 4,
                                    'dorm' => 8,
                                    default => 0,
                                });
                            }),

                        TextInput::make('current_occupancy')
                            ->numeric(),
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
                            ->required()
                            ->live(),

                        TextInput::make('base_rate')
                            ->numeric()
                            ->required()
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

                        Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('room_number')
            ->columns([
                TextColumn::make('room_number')
                    ->label('Room No.'),
                TextColumn::make('block.name')
                    ->label('Block'),
                TextColumn::make('floor.name')
                    ->label('Floor'),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'single' => 'primary',
                        'double' => 'info',
                        'triple' => 'warning',
                        'quad' => 'danger',
                        'dorm' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('gender_policy')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'male' => 'primary',
                        'female' => 'info',
                        'mixed' => 'warning',
                        'inherit_hostel' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('base_rate')
                    ->money('GHS'),
                TextColumn::make('billing_cycle'),
                TextColumn::make('current_occupancy')
                    ->label('Occupancy')
                    ->formatStateUsing(fn (Room $record): string => "{$record->current_occupancy}/{$record->max_occupancy}"),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'partially_occupied' => 'warning',
                        'full' => 'danger',
                        'maintenance' => 'info',
                        'closed' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('beds_count')
                    ->label('Beds')
                    ->counts('beds'),
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
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
