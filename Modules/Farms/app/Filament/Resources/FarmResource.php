<?php

namespace Modules\Farms\Filament\Resources;

use EduardoRibeiroDev\FilamentLeaflet\Fields\MapPicker;
use EduardoRibeiroDev\FilamentLeaflet\Tables\MapColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Farms\Filament\Resources\FarmResource\Pages;
use Modules\Farms\Filament\Resources\FarmResource\RelationManagers;
use Modules\Farms\Models\Farm;

class FarmResource extends Resource
{
    protected static ?string $model = Farm::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sun';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Farm Details')->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('location')->maxLength(255),
                ]),
                Grid::make(3)->schema([
                    Select::make('type')
                        ->options(array_combine(Farm::TYPES, array_map('ucfirst', Farm::TYPES)))
                        ->default('mixed')
                        ->required(),
                    TextInput::make('total_area')->label('Total Area')->numeric()->step(0.0001),
                    Select::make('area_unit')
                        ->options(['acres' => 'Acres', 'hectares' => 'Hectares'])
                        ->default('acres'),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('owner_name')->label('Owner Name')->maxLength(255),
                    TextInput::make('owner_phone')->label('Owner Phone')->maxLength(50),
                ]),
                Select::make('status')
                    ->options(array_combine(Farm::STATUSES, array_map('ucfirst', Farm::STATUSES)))
                    ->default('active'),
                Textarea::make('description')->rows(3)->columnSpanFull(),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),

            Section::make('"Meet the Farm" Profile')
                ->description('Public shop profile — story, media, and gallery')
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextInput::make('established_year')
                        ->label('Established Year')
                        ->numeric()
                        ->minValue(1900)
                        ->maxValue(now()->year)
                        ->placeholder(now()->year),

                    Textarea::make('about')
                        ->label('Farm Story')
                        ->rows(4)
                        ->columnSpanFull()
                        ->placeholder('Tell customers your story — how the farm started, what you grow, your values…'),

                    FileUpload::make('cover_image')
                        ->label('Cover Photo')
                        ->image()
                        ->directory('farm-profiles')
                        ->columnSpanFull(),

                    FileUpload::make('gallery_images')
                        ->label('Gallery Photos')
                        ->multiple()
                        ->image()
                        ->directory('farm-gallery')
                        ->columnSpanFull(),

                    TextInput::make('video_url')
                        ->label('Video URL (YouTube embed)')
                        ->url()
                        ->maxLength(500)
                        ->placeholder('https://www.youtube.com/embed/...')
                        ->columnSpanFull(),
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

                    \Filament\Schemas\Components\Grid::make(2)
                        ->schema([
                            TextInput::make('latitude')->numeric()->readOnly()->placeholder('Auto-filled by map pin'),
                            TextInput::make('longitude')->numeric()->readOnly()->placeholder('Auto-filled by map pin'),
                        ])
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('location'),
                TextColumn::make('type')->badge(),
                TextColumn::make('total_area')->label('Area')->numeric(decimalPlaces: 2),
                TextColumn::make('area_unit')->label('Unit'),
                TextColumn::make('owner_name')->label('Owner'),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'   => 'success',
                        'inactive' => 'gray',
                        'fallow'   => 'warning',
                        default    => 'gray',
                    }),
                MapColumn::make('latitude')
                    ->latitudeFieldName('latitude')
                    ->longitudeFieldName('longitude')
                    ->label('Map Preview')
                    ->height(80)
                    ->zoom(13)
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(array_combine(Farm::TYPES, array_map('ucfirst', Farm::TYPES))),
                SelectFilter::make('status')
                    ->options(array_combine(Farm::STATUSES, array_map('ucfirst', Farm::STATUSES))),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CropCyclesRelationManager::class,
            RelationManagers\LivestockBatchesRelationManager::class,
            RelationManagers\HarvestRecordsRelationManager::class,
            RelationManagers\FarmExpensesRelationManager::class,
            RelationManagers\FarmPlotsRelationManager::class,
            RelationManagers\FarmEquipmentRelationManager::class,
            RelationManagers\WeatherLogsRelationManager::class,
            RelationManagers\SoilTestRecordsRelationManager::class,
            RelationManagers\FarmDocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarms::route('/'),
            'create' => Pages\CreateFarm::route('/create'),
            'view'   => Pages\ViewFarm::route('/{record}'),
            'edit'   => Pages\EditFarm::route('/{record}/edit'),
        ];
    }
}
