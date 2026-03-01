<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Farms\Filament\Resources\FarmEquipmentResource\Pages;
use Modules\Farms\Models\FarmEquipment;

class FarmEquipmentResource extends Resource
{
    protected static ?string $model = FarmEquipment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 13;

    protected static ?string $navigationLabel = 'Farm Equipment';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Equipment Details')
                ->columns(3)
                ->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    TextInput::make('name')->required()->maxLength(255),

                    Select::make('equipment_type')
                        ->options(array_combine(
                            FarmEquipment::EQUIPMENT_TYPES,
                            array_map('ucfirst', FarmEquipment::EQUIPMENT_TYPES)
                        ))
                        ->default('other')
                        ->required(),

                    TextInput::make('make')->label('Make/Brand')->maxLength(100),
                    TextInput::make('model')->maxLength(100),
                    TextInput::make('year')->numeric()->minValue(1950)->maxValue(2099),
                    TextInput::make('serial_number')->maxLength(100),

                    Select::make('status')
                        ->options([
                            'active'      => 'Active',
                            'maintenance' => 'In Maintenance',
                            'retired'     => 'Retired',
                        ])
                        ->default('active')
                        ->required(),
                ]),

            Section::make('Financial & Service')
                ->columns(2)
                ->schema([
                    DatePicker::make('purchase_date'),
                    TextInput::make('purchase_price')->label('Purchase Price (GHS)')->numeric()->prefix('GHS')->step(0.01),
                    TextInput::make('current_value')->label('Current Value (GHS)')->numeric()->prefix('GHS')->step(0.01),
                    DatePicker::make('last_service_date'),
                    DatePicker::make('next_service_date'),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([Textarea::make('notes')->rows(2)->columnSpanFull()]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('farm.name')->label('Farm')->sortable(),
                TextColumn::make('name')->searchable()->limit(30),
                TextColumn::make('equipment_type')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('make'),
                TextColumn::make('model'),
                TextColumn::make('year'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'      => 'success',
                        'maintenance' => 'warning',
                        'retired'     => 'danger',
                        default       => 'gray',
                    }),

                TextColumn::make('purchase_price')->money('GHS')->label('Purchase')->toggleable(),
                TextColumn::make('current_value')->money('GHS')->label('Value')->toggleable(),
                TextColumn::make('next_service_date')->date()->label('Next Service')->sortable(),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('equipment_type')->options(
                    array_combine(FarmEquipment::EQUIPMENT_TYPES, array_map('ucfirst', FarmEquipment::EQUIPMENT_TYPES))
                ),
                SelectFilter::make('status')->options([
                    'active' => 'Active', 'maintenance' => 'In Maintenance', 'retired' => 'Retired',
                ]),
            ])
            ->actions([ViewAction::make(), EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmEquipment::route('/'),
            'create' => Pages\CreateFarmEquipment::route('/create'),
            'view'   => Pages\ViewFarmEquipment::route('/{record}'),
            'edit'   => Pages\EditFarmEquipment::route('/{record}/edit'),
        ];
    }
}