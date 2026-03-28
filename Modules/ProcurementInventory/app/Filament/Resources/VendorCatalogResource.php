<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\VendorCatalogResource\Pages;
use Modules\ProcurementInventory\Filament\Resources\VendorCatalogResource\RelationManagers\CatalogItemsRelationManager;
use Modules\ProcurementInventory\Models\Vendor;
use Modules\ProcurementInventory\Models\VendorCatalog;

class VendorCatalogResource extends Resource
{
    protected static ?string $model = VendorCatalog::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?string $navigationLabel = 'Price Catalogs';

    protected static ?int $navigationSort = 27;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Catalog Details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('vendor_id')
                        ->label('Vendor')
                        ->options(function () {
                            $companyId = filament()->getTenant()?->id ?? auth()->user()?->current_company_id;
                            return Vendor::query()
                                ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                                ->where('status', 'active')
                                ->pluck('name', 'id');
                        })
                        ->required()
                        ->searchable(),

                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\DatePicker::make('effective_from')
                        ->required()
                        ->default(now()),

                    Forms\Components\DatePicker::make('effective_to')
                        ->nullable()
                        ->label('Effective To (leave blank = open-ended)'),

                    Forms\Components\Toggle::make('is_active')
                        ->default(true),
                ]),

            Section::make('Description')
                ->schema([
                    Forms\Components\Textarea::make('description')->rows(3)->nullable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('vendor.name')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('effective_from')
                    ->date(),

                Tables\Columns\TextColumn::make('effective_to')
                    ->date()
                    ->placeholder('Open')
                    ->color(fn ($record) => $record->effective_to?->isPast() ? 'danger' : null),

                Tables\Columns\TextColumn::make('catalog_items_count')
                    ->counts('catalogItems')
                    ->label('Items'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vendor')
                    ->relationship('vendor', 'name'),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CatalogItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVendorCatalogs::route('/'),
            'create' => Pages\CreateVendorCatalog::route('/create'),
            'edit'   => Pages\EditVendorCatalog::route('/{record}/edit'),
        ];
    }
}