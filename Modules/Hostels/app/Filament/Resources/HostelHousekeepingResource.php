<?php

namespace Modules\Hostels\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Hostels\Filament\Resources\HostelHousekeepingResource\Pages\CreateHostelHousekeeping;
use Modules\Hostels\Filament\Resources\HostelHousekeepingResource\Pages\EditHostelHousekeeping;
use Modules\Hostels\Filament\Resources\HostelHousekeepingResource\Pages\ListHostelHousekeepings;
use Modules\Hostels\Models\HostelHousekeepingSchedule;

class HostelHousekeepingResource extends Resource
{
    protected static ?string $model = HostelHousekeepingSchedule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|\UnitEnum|null $navigationGroup = 'Staff Management';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Cleaning Assignment')
                    ->schema([
                        Select::make('hostel_id')
                            ->relationship('hostel', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('room_id')
                            ->relationship('room', 'room_number')
                            ->searchable()
                            ->preload(),
                        Select::make('assigned_employee_id')
                            ->relationship('assignedEmployee', 'full_name')
                            ->searchable()
                            ->preload(),
                    ])->columns(3),

                Section::make('Schedule Details')
                    ->schema([
                        DatePicker::make('schedule_date')
                            ->required()
                            ->default(now()),
                        Select::make('cleaning_type')
                            ->options([
                                'daily' => 'Daily Cleaning',
                                'deep' => 'Deep Cleaning',
                                'checkout' => 'After Checkout',
                                'maintenance' => 'Maintenance Cleaning',
                                'special' => 'Special Request',
                            ])
                            ->default('daily')
                            ->required(),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('pending')
                            ->required(),
                    ])->columns(3),

                Section::make('Execution Details')
                    ->schema([
                        DateTimePicker::make('started_at')
                            ->nullable(),
                        DateTimePicker::make('completed_at')
                            ->nullable(),
                        TextInput::make('quality_score')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10)
                            ->step(1),
                        Textarea::make('notes')
                            ->maxLength(65535),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hostel.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('room.room_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assignedEmployee.full_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('schedule_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('cleaning_type')
                    ->badge()
                    ->colors([
                        'daily' => 'primary',
                        'deep' => 'warning',
                        'checkout' => 'info',
                        'maintenance' => 'success',
                        'special' => 'danger',
                    ])
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    ])
                    ->sortable(),
                TextColumn::make('quality_score')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_completed')
                    ->getStateUsing(fn ($record) => $record->status === 'completed')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHostelHousekeepings::route('/'),
            'create' => CreateHostelHousekeeping::route('/create'),
            'edit' => EditHostelHousekeeping::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
