<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Facades\Filament;
use Modules\Farms\Filament\Resources\FarmEquipmentLogResource\Pages;
use Modules\Farms\Models\FarmEquipmentLog;
use Modules\Farms\Models\FarmEquipment;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmPlot;
use Modules\Farms\Models\FarmWorker;

class FarmEquipmentLogResource extends Resource
{
    protected static ?string $model = FarmEquipmentLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string|\UnitEnum|null $navigationGroup = 'Farm Operations';

    protected static ?string $navigationLabel = 'Equipment Logs';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Operation Details')
                ->columns(2)
                ->schema([
                    Select::make('farm_equipment_id')
                        ->label('Equipment')
                        ->options(fn () => FarmEquipment::where('company_id', Filament::getTenant()?->id)
                            ->pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    Select::make('farm_id')
                        ->label('Farm')
                        ->options(fn () => Farm::where('company_id', Filament::getTenant()?->id)
                            ->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->live(),

                    Select::make('farm_plot_id')
                        ->label('Plot (optional)')
                        ->options(fn (\Filament\Forms\Get $get) => FarmPlot::where('farm_id', $get('farm_id'))->pluck('name', 'id'))
                        ->nullable(),

                    Select::make('operator_worker_id')
                        ->label('Operator')
                        ->options(fn () => FarmWorker::where('company_id', Filament::getTenant()?->id)
                            ->where('is_active', true)
                            ->pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),

                    Select::make('operation_type')
                        ->options(array_combine(
                            FarmEquipmentLog::OPERATION_TYPES,
                            FarmEquipmentLog::OPERATION_TYPES
                        ))
                        ->required(),

                    DateTimePicker::make('started_at')->required(),
                    DateTimePicker::make('ended_at')->nullable(),
                ]),

            Section::make('Metrics')
                ->columns(3)
                ->schema([
                    TextInput::make('hours_used')->numeric()->step(0.01)->nullable(),
                    TextInput::make('area_covered_ha')->label('Area Covered (ha)')->numeric()->step(0.0001)->nullable(),
                    TextInput::make('fuel_used_litres')->label('Fuel Used (L)')->numeric()->step(0.01)->nullable(),
                    TextInput::make('fuel_cost')->label('Fuel Cost')->numeric()->step(0.01)->nullable(),
                    TextInput::make('cost_per_ha')->label('Cost/ha')->numeric()->disabled()->dehydrated(false)->nullable(),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([
                    Textarea::make('notes')->rows(3)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('equipment.name')->label('Equipment')->sortable()->searchable(),
                TextColumn::make('farm.name')->label('Farm')->sortable(),
                TextColumn::make('operation_type')->badge()->color('info'),
                TextColumn::make('started_at')->label('Started')->dateTime()->sortable(),
                TextColumn::make('hours_used')->label('Hours')->numeric(2),
                TextColumn::make('area_covered_ha')->label('Area (ha)')->numeric(4),
                TextColumn::make('fuel_cost')->label('Fuel Cost')->money('GHS'),
                TextColumn::make('cost_per_ha')->label('Cost/ha')->numeric(4),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('operation_type')->options(
                    array_combine(FarmEquipmentLog::OPERATION_TYPES, FarmEquipmentLog::OPERATION_TYPES)
                ),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('started_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmEquipmentLogs::route('/'),
            'create' => Pages\CreateFarmEquipmentLog::route('/create'),
            'edit'   => Pages\EditFarmEquipmentLog::route('/{record}/edit'),
        ];
    }
}