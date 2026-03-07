<?php

namespace Modules\ManufacturingWater\Filament\Resources;

use EduardoRibeiroDev\FilamentLeaflet\Fields\MapPicker;
use EduardoRibeiroDev\FilamentLeaflet\Infolists\MapEntry;
use EduardoRibeiroDev\FilamentLeaflet\Tables\MapColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\ManufacturingWater\Filament\Resources\MwPlantResource\Pages;
use Modules\ManufacturingWater\Filament\Resources\MwPlantResource\RelationManagers;
use Modules\ManufacturingWater\Models\MwPlant;

class MwPlantResource extends Resource
{
    protected static ?string $model = MwPlant::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturing Water';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Plant Details')->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('location')->maxLength(255),
                ]),
                Grid::make(3)->schema([
                    Select::make('type')
                        ->options(array_combine(MwPlant::TYPES, array_map('ucfirst', MwPlant::TYPES)))
                        ->default('treatment')
                        ->required(),
                    Select::make('source_type')
                        ->label('Water Source')
                        ->options(array_combine(MwPlant::SOURCE_TYPES, array_map('ucfirst', MwPlant::SOURCE_TYPES)))
                        ->nullable(),
                    Select::make('status')
                        ->options(array_combine(MwPlant::STATUSES, array_map('ucfirst', MwPlant::STATUSES)))
                        ->default('active')
                        ->required(),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('capacity_liters_per_day')->label('Capacity (Litres/Day)')->numeric()->step(0.01),
                    TextInput::make('slug')->maxLength(255),
                ]),
                Textarea::make('description')->rows(3)->columnSpanFull(),
            ]),

            Section::make('Location & Map')
                ->icon('heroicon-o-map-pin')
                ->collapsible()
                ->columns(2)
                ->schema([
                    MapPicker::make('map')
                        ->label('Pin Location & Draw Boundary')
                        ->latitudeFieldName('latitude')
                        ->longitudeFieldName('longitude')
                        ->center(5.6037, -0.1870)
                        ->height(450)
                        ->zoom(12)
                        ->fullscreenControl()
                        ->searchControl()
                        ->scaleControl()
                        ->drawPolygonControl()
                        ->drawPolylineControl()
                        ->drawRectangleControl()
                        ->drawCircleControl()
                        ->editLayersControl()
                        ->dragLayersControl()
                        ->removeLayersControl()
                        ->columnSpanFull(),

                    Grid::make(2)
                        ->schema([
                            TextInput::make('latitude')->numeric()->readOnly()->placeholder('Auto-filled by map pin'),
                            TextInput::make('longitude')->numeric()->readOnly()->placeholder('Auto-filled by map pin'),
                        ])
                        ->columnSpanFull(),

                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Plant Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('name')->weight('bold'),
                    TextEntry::make('type')->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'treatment'    => 'primary',
                            'bottling'     => 'success',
                            'distribution' => 'info',
                            default        => 'gray',
                        }),
                    TextEntry::make('status')->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'active'         => 'success',
                            'idle'           => 'warning',
                            'maintenance'    => 'danger',
                            'decommissioned' => 'gray',
                            default          => 'gray',
                        }),
                    TextEntry::make('source_type')->label('Water Source')->placeholder('—'),
                    TextEntry::make('capacity_liters_per_day')->label('Capacity (L/day)')->placeholder('—'),
                    TextEntry::make('location')->placeholder('—'),
                    TextEntry::make('description')->columnSpanFull()->placeholder('—'),
                ]),

            Section::make('Location & Map')
                ->icon('heroicon-o-map-pin')
                ->collapsible()
                ->columns(2)
                ->schema([
                    TextEntry::make('location')->label('Address')->placeholder('—')->columnSpanFull(),

                    MapEntry::make('map')
                        ->label('Plant Location')
                        ->latitudeFieldName('latitude')
                        ->longitudeFieldName('longitude')
                        ->center(5.6037, -0.1870)
                        ->height(400)
                        ->zoom(14)
                        ->static()
                        ->fullscreenControl()
                        ->scaleControl()
                        ->columnSpanFull(),

                    TextEntry::make('latitude')->placeholder('—'),
                    TextEntry::make('longitude')->placeholder('—'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('location'),
                TextColumn::make('type')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'treatment'    => 'primary',
                        'bottling'     => 'success',
                        'distribution' => 'info',
                        default        => 'gray',
                    }),
                TextColumn::make('source_type')->label('Source'),
                TextColumn::make('capacity_liters_per_day')->label('Capacity (L/day)')->numeric(decimalPlaces: 0),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'         => 'success',
                        'idle'           => 'warning',
                        'maintenance'    => 'danger',
                        'decommissioned' => 'gray',
                        default          => 'gray',
                    }),
                MapColumn::make('latitude')
                    ->label('Map Preview')
                    ->latitudeFieldName('latitude')
                    ->longitudeFieldName('longitude')
                    ->height(80)
                    ->zoom(13)
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(array_combine(MwPlant::TYPES, array_map('ucfirst', MwPlant::TYPES))),
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(MwPlant::STATUSES, array_map('ucfirst', MwPlant::STATUSES))),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TreatmentStagesRelationManager::class,
            RelationManagers\WaterTestRecordsRelationManager::class,
            RelationManagers\TankLevelsRelationManager::class,
            RelationManagers\ChemicalUsagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMwPlants::route('/'),
            'create' => Pages\CreateMwPlant::route('/create'),
            'view'   => Pages\ViewMwPlant::route('/{record}'),
            'edit'   => Pages\EditMwPlant::route('/{record}/edit'),
        ];
    }
}