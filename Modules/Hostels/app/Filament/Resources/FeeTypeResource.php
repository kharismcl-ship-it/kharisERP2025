<?php

namespace Modules\Hostels\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Hostels\Filament\Resources\FeeTypeResource\Pages;
use Modules\Hostels\Models\FeeType;

class FeeTypeResource extends Resource
{
    protected static ?string $model = FeeType::class;

    protected static ?string $slug = 'fee-types';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('hostel_id')
                    ->relationship('hostel', 'name')
                    ->searchable()
                    ->required(),

                TextInput::make('name')
                    ->required(),

                TextInput::make('code')
                    ->required(),

                TextInput::make('default_amount')
                    ->numeric()
                    ->required(),

                Select::make('billing_cycle')
                    ->options([
                        'one_time' => 'One Time',
                        'per_semester' => 'Per Semester',
                        'per_year' => 'Per Year',
                        'per_night' => 'Per Night',
                    ])
                    ->required(),

                Toggle::make('is_mandatory')
                    ->default(false),

                Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hostel.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('default_amount')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('billing_cycle')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('is_mandatory')
                    ->badge()
                    ->sortable(),

                TextColumn::make('is_active')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeeTypes::route('/'),
            'create' => Pages\CreateFeeType::route('/create'),
            'edit' => Pages\EditFeeType::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
