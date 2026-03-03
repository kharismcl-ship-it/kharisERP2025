<?php

namespace Modules\ManufacturingPaper\Filament\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\ManufacturingPaper\Filament\Resources\MpEquipmentLogResource\Pages;
use Modules\ManufacturingPaper\Models\MpEquipmentLog;

class MpEquipmentLogResource extends Resource
{
    protected static ?string $model = MpEquipmentLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturing Paper';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Equipment Logs';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Equipment Details')->schema([
                Grid::make(2)->schema([
                    Select::make('plant_id')
                        ->label('Plant')
                        ->relationship('plant', 'name')
                        ->searchable()
                        ->required(),
                    Select::make('production_line_id')
                        ->label('Production Line')
                        ->relationship('productionLine', 'name')
                        ->searchable()
                        ->nullable(),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('equipment_name')->required()->maxLength(255),
                    Select::make('log_type')
                        ->label('Log Type')
                        ->options(array_combine(
                            MpEquipmentLog::LOG_TYPES,
                            array_map('ucfirst', MpEquipmentLog::LOG_TYPES)
                        ))
                        ->required(),
                ]),
                Textarea::make('description')->rows(3)->required()->columnSpanFull(),
            ]),

            Section::make('Status & Cost')->schema([
                Grid::make(3)->schema([
                    Select::make('status')
                        ->options(array_combine(
                            MpEquipmentLog::STATUSES,
                            array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), MpEquipmentLog::STATUSES))
                        ))
                        ->default('open')
                        ->required(),
                    DateTimePicker::make('logged_at')->label('Logged At')->default(now()),
                    DateTimePicker::make('resolved_at')->label('Resolved At')->nullable(),
                ]),
                TextInput::make('cost')->label('Repair Cost')->numeric()->prefix('GHS')->step(0.01)->nullable(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plant.name')->label('Plant')->searchable()->sortable(),
                TextColumn::make('equipment_name')->label('Equipment')->searchable()->sortable(),
                TextColumn::make('log_type')->label('Type')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'breakdown'   => 'danger',
                        'maintenance' => 'warning',
                        'inspection'  => 'info',
                        'upgrade'     => 'success',
                        'calibration' => 'primary',
                        default       => 'gray',
                    }),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open'        => 'danger',
                        'in_progress' => 'warning',
                        'resolved'    => 'success',
                        'closed'      => 'gray',
                        default       => 'gray',
                    }),
                TextColumn::make('cost')->label('Cost')->money('GHS')->placeholder('—'),
                TextColumn::make('logged_at')->label('Logged')->dateTime()->sortable(),
                TextColumn::make('resolved_at')->label('Resolved')->dateTime()->placeholder('Pending'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('plant_id')
                    ->label('Plant')
                    ->relationship('plant', 'name'),
                Tables\Filters\SelectFilter::make('log_type')
                    ->label('Log Type')
                    ->options(array_combine(
                        MpEquipmentLog::LOG_TYPES,
                        array_map('ucfirst', MpEquipmentLog::LOG_TYPES)
                    )),
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(
                        MpEquipmentLog::STATUSES,
                        array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), MpEquipmentLog::STATUSES))
                    )),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ])
            ->defaultSort('logged_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMpEquipmentLogs::route('/'),
            'create' => Pages\CreateMpEquipmentLog::route('/create'),
            'view'   => Pages\ViewMpEquipmentLog::route('/{record}'),
            'edit'   => Pages\EditMpEquipmentLog::route('/{record}/edit'),
        ];
    }
}
