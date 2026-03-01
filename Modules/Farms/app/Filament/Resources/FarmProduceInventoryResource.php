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
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Farms\Filament\Resources\FarmProduceInventoryResource\Pages;
use Modules\Farms\Models\FarmProduceInventory;

class FarmProduceInventoryResource extends Resource
{
    protected static ?string $model = FarmProduceInventory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 11;

    protected static ?string $navigationLabel = 'Produce Inventory';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Produce Details')
                ->columns(3)
                ->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('crop_cycle_id')
                        ->label('Crop Cycle (optional)')
                        ->relationship('cropCycle', 'crop_name')
                        ->searchable()
                        ->nullable(),

                    TextInput::make('product_name')->required()->maxLength(255),

                    TextInput::make('unit')->label('Unit (kg/bags/crates)')->maxLength(50),

                    Select::make('status')
                        ->options([
                            'in_stock' => 'In Stock',
                            'low_stock' => 'Low Stock',
                            'depleted'  => 'Depleted',
                        ])
                        ->default('in_stock')
                        ->required(),

                    TextInput::make('storage_location')->maxLength(255),
                ]),

            Section::make('Stock Quantities')
                ->columns(4)
                ->schema([
                    TextInput::make('total_quantity')->label('Total Harvested')->numeric()->step(0.001)->default(0),
                    TextInput::make('current_stock')->label('Current Stock')->numeric()->step(0.001)->default(0),
                    TextInput::make('reserved_stock')->label('Reserved')->numeric()->step(0.001)->default(0),
                    TextInput::make('sold_stock')->label('Sold')->numeric()->step(0.001)->default(0),
                    TextInput::make('unit_cost')->label('Avg Unit Cost (GHS)')->numeric()->step(0.0001)->prefix('GHS'),
                ]),

            Section::make('Dates')
                ->columns(2)
                ->schema([
                    DatePicker::make('harvest_date'),
                    DatePicker::make('expiry_date')->label('Expiry Date (perishables)'),
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
                TextColumn::make('farm.name')->label('Farm')->sortable()->searchable(),
                TextColumn::make('product_name')->searchable()->limit(30),
                TextColumn::make('unit')->label('Unit'),

                TextColumn::make('current_stock')->label('In Stock')->numeric(3),
                TextColumn::make('reserved_stock')->label('Reserved')->numeric(3),
                TextColumn::make('sold_stock')->label('Sold')->numeric(3),

                TextColumn::make('unit_cost')->money('GHS')->label('Unit Cost'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'in_stock'  => 'success',
                        'low_stock' => 'warning',
                        'depleted'  => 'danger',
                        default     => 'gray',
                    }),

                TextColumn::make('harvest_date')->date()->toggleable(),
                TextColumn::make('expiry_date')->date()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('status')->options([
                    'in_stock' => 'In Stock',
                    'low_stock' => 'Low Stock',
                    'depleted'  => 'Depleted',
                ]),
            ])
            ->actions([ViewAction::make(), EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('harvest_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmProduceInventories::route('/'),
            'create' => Pages\CreateFarmProduceInventory::route('/create'),
            'view'   => Pages\ViewFarmProduceInventory::route('/{record}'),
            'edit'   => Pages\EditFarmProduceInventory::route('/{record}/edit'),
        ];
    }
}