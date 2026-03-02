<?php

namespace Modules\Sales\Filament\Resources;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Sales\Filament\Resources\DiningTableResource\Pages;
use Modules\Sales\Models\DiningTable;

class DiningTableResource extends Resource
{
    protected static ?string $model = DiningTable::class;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-table-cells';
    protected static string|\UnitEnum|null   $navigationGroup = 'Restaurant';
    protected static ?int                    $navigationSort  = 50;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dining Table')->columns(2)->schema([
                Select::make('restaurant_id')
                    ->label('Restaurant')
                    ->relationship('restaurant', 'name')
                    ->required()->searchable()->preload(),
                TextInput::make('table_number')->required()->maxLength(20),
                TextInput::make('section')->maxLength(100),
                TextInput::make('capacity')->numeric()->default(4),
                Select::make('status')
                    ->options(array_combine(DiningTable::STATUSES, array_map('ucfirst', DiningTable::STATUSES)))
                    ->default('available'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('restaurant.name'),
                TextColumn::make('table_number')->searchable()->sortable(),
                TextColumn::make('section'),
                TextColumn::make('capacity'),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state) => match ($state) {
                        'available' => 'success',
                        'occupied'  => 'danger',
                        'reserved'  => 'warning',
                        'cleaning'  => 'gray',
                        default     => 'info',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')->options(array_combine(DiningTable::STATUSES, array_map('ucfirst', DiningTable::STATUSES))),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDiningTables::route('/'),
            'create' => Pages\CreateDiningTable::route('/create'),
            'edit'   => Pages\EditDiningTable::route('/{record}/edit'),
        ];
    }
}