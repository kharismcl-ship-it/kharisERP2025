<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Facades\Filament;
use Modules\Farms\Filament\Resources\FarmStorageLocationResource\Pages;
use Modules\Farms\Models\FarmStorageLocation;
use Modules\Farms\Models\Farm;

class FarmStorageLocationResource extends Resource
{
    protected static ?string $model = FarmStorageLocation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static string|\UnitEnum|null $navigationGroup = 'Farm Operations';

    protected static ?string $navigationLabel = 'Storage Locations';

    protected static ?int $navigationSort = 27;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Storage Details')
                ->columns(2)
                ->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->options(fn () => Farm::where('company_id', Filament::getTenant()?->id)->pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    TextInput::make('name')->required()->maxLength(200),

                    Select::make('type')
                        ->options([
                            'silo'          => 'Silo',
                            'cold_room'     => 'Cold Room',
                            'warehouse'     => 'Warehouse',
                            'outdoor_stack' => 'Outdoor Stack',
                            'other'         => 'Other',
                        ])
                        ->required(),

                    TextInput::make('capacity_tonnes')->label('Capacity (tonnes)')->numeric()->step(0.01)->nullable(),
                    TextInput::make('current_stock_tonnes')->label('Current Stock (tonnes)')->numeric()->step(0.01)->default(0),
                    TextInput::make('temperature_c')->label('Temperature (°C)')->numeric()->step(0.01)->nullable(),
                    TextInput::make('humidity_pct')->label('Humidity (%)')->numeric()->step(0.01)->nullable(),
                    Toggle::make('is_active')->label('Active')->default(true),
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
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('farm.name')->sortable(),
                TextColumn::make('type')->badge()->color('info')
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))),
                TextColumn::make('capacity_tonnes')->label('Capacity (t)')->numeric(2),
                TextColumn::make('current_stock_tonnes')->label('Stock (t)')->numeric(2),
                TextColumn::make('utilization')
                    ->label('Utilization %')
                    ->getStateUsing(fn (FarmStorageLocation $record): string => $record->capacity_tonnes > 0
                        ? round(($record->current_stock_tonnes / $record->capacity_tonnes) * 100, 1) . '%'
                        : '—'),
                TextColumn::make('temperature_c')->label('Temp (°C)')->numeric(1)->placeholder('—'),
                TextColumn::make('humidity_pct')->label('Humidity %')->numeric(1)->placeholder('—'),
                TextColumn::make('last_checked_at')->label('Last Checked')->dateTime()->placeholder('—'),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('type')->options([
                    'silo' => 'Silo', 'cold_room' => 'Cold Room', 'warehouse' => 'Warehouse',
                    'outdoor_stack' => 'Outdoor Stack', 'other' => 'Other',
                ]),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmStorageLocations::route('/'),
            'create' => Pages\CreateFarmStorageLocation::route('/create'),
            'edit'   => Pages\EditFarmStorageLocation::route('/{record}/edit'),
        ];
    }
}