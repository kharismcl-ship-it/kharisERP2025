<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use EduardoRibeiroDev\FilamentLeaflet\Fields\MapPicker;
use EduardoRibeiroDev\FilamentLeaflet\Infolists\MapEntry;
use EduardoRibeiroDev\FilamentLeaflet\Tables\MapColumn;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Modules\ProcurementInventory\Filament\Resources\WarehouseResource\Pages;
use Modules\ProcurementInventory\Models\Warehouse;

class WarehouseResource extends Resource
{
    protected static ?string $model = Warehouse::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Section::make('Warehouse Details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('company_id')
                        ->relationship('company', 'name')
                        
                        ->searchable(),

                    Forms\Components\TextInput::make('name')
                        
                        ->maxLength(150)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, callable $set, $get) {
                            if (! $get('code')) {
                                $set('code', strtoupper(Str::slug($state, '-')));
                            }
                        }),

                    Forms\Components\TextInput::make('code')
                        
                        ->maxLength(20)
                        ->helperText('Short identifier, e.g. WH-01')
                        ->afterStateUpdated(fn ($state, callable $set) => $set('code', strtoupper($state))),

                    Forms\Components\Toggle::make('is_default')
                        ->label('Default Warehouse')
                        ->helperText('Stock receipts go here when no warehouse is specified.')
                        ->default(false),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ]),

            Forms\Components\Section::make('Location & Contact')
                ->columns(2)
                ->schema([
                    Forms\Components\Textarea::make('address')->rows(2),
                    Forms\Components\TextInput::make('city'),
                    Forms\Components\TextInput::make('contact_person'),
                    Forms\Components\TextInput::make('contact_phone'),
                ]),

            Section::make('Map Location')
                ->icon('heroicon-o-map-pin')
                ->collapsible()
                ->columns(2)
                ->schema([
                    MapPicker::make('map')
                        ->label('Pin Warehouse Location')
                        ->latitudeFieldName('latitude')
                        ->longitudeFieldName('longitude')
                        ->center(5.6037, -0.1870)
                        ->height(400)
                        ->zoom(14)
                        ->fullscreenControl()
                        ->searchControl()
                        ->scaleControl()
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('latitude')->numeric()->readOnly()->placeholder('Auto-filled by map pin'),
                    Forms\Components\TextInput::make('longitude')->numeric()->readOnly()->placeholder('Auto-filled by map pin'),
                ]),

            Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Warehouse Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('code')->badge()->color('info')->label('Code'),
                    TextEntry::make('name')->weight('bold'),
                    TextEntry::make('city')->placeholder('—'),
                    TextEntry::make('address')->columnSpanFull()->placeholder('—'),
                    TextEntry::make('contact_person')->placeholder('—'),
                    TextEntry::make('contact_phone')->placeholder('—'),
                    IconEntry::make('is_default')->label('Default')->boolean(),
                    IconEntry::make('is_active')->label('Active')->boolean(),
                    TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
                ]),

            Section::make('Map Location')
                ->icon('heroicon-o-map-pin')
                ->collapsible()
                ->columns(2)
                ->schema([
                    MapEntry::make('map')
                        ->label('Warehouse Location')
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
                Tables\Columns\TextColumn::make('code')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('city')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('contact_person')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('stock_levels_count')
                    ->counts('stockLevels')
                    ->label('Items'),

                Tables\Columns\IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                MapColumn::make('latitude')
                    ->label('Map')
                    ->latitudeFieldName('latitude')
                    ->longitudeFieldName('longitude')
                    ->height(80)
                    ->zoom(13)
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
                Tables\Filters\TernaryFilter::make('is_default')->label('Default'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWarehouses::route('/'),
            'create' => Pages\CreateWarehouse::route('/create'),
            'view'   => Pages\ViewWarehouse::route('/{record}'),
            'edit'   => Pages\EditWarehouse::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'code', 'city'];
    }
}
