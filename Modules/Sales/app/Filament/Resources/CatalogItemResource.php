<?php

namespace Modules\Sales\Filament\Resources;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Sales\Filament\Resources\CatalogItemResource\Pages;
use Modules\Sales\Models\SalesCatalog;

class CatalogItemResource extends Resource
{
    protected static ?string $model      = SalesCatalog::class;
    protected static ?string $modelLabel = 'Catalog Item';

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-squares-2x2';
    protected static string|\UnitEnum|null   $navigationGroup = 'Catalog';
    protected static ?int                    $navigationSort  = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Catalog Item')->columns(2)->schema([
                TextInput::make('name')->required()->maxLength(255)->columnSpanFull(),
                Select::make('source_module')
                    ->options(array_combine(SalesCatalog::SOURCE_MODULES, SalesCatalog::SOURCE_MODULES))
                    ->required(),
                TextInput::make('sku')->maxLength(100),
                TextInput::make('unit')->default('pcs')->maxLength(50),
                TextInput::make('base_price')->numeric()->prefix('GHS')->required(),
                TextInput::make('tax_rate')->numeric()->suffix('%')->default(15),
                Select::make('availability_mode')
                    ->options(array_combine(SalesCatalog::AVAILABILITY_MODES, array_map('ucfirst', SalesCatalog::AVAILABILITY_MODES)))
                    ->default('always'),
                Toggle::make('is_active')->default(true)->inline(false),
                Textarea::make('description')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('source_module')->badge()->label('Source')
                    ->color(fn (string $state) => match ($state) {
                        'ManufacturingWater' => 'info',
                        'ManufacturingPaper' => 'gray',
                        'Farms'              => 'success',
                        'ProcurementInventory' => 'warning',
                        'Fleet'              => 'danger',
                        'Hostels'            => 'primary',
                        default              => 'gray',
                    }),
                TextColumn::make('sku'),
                TextColumn::make('unit'),
                TextColumn::make('base_price')->money('GHS')->sortable(),
                TextColumn::make('tax_rate')->suffix('%'),
                TextColumn::make('availability_mode')->badge(),
                ToggleColumn::make('is_active'),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('source_module')
                    ->options(array_combine(SalesCatalog::SOURCE_MODULES, SalesCatalog::SOURCE_MODULES)),
                TernaryFilter::make('is_active'),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCatalogItems::route('/'),
            'create' => Pages\CreateCatalogItem::route('/create'),
            'edit'   => Pages\EditCatalogItem::route('/{record}/edit'),
        ];
    }
}