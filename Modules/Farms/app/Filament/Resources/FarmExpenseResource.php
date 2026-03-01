<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Farms\Filament\Resources\FarmExpenseResource\Pages;
use Modules\Farms\Models\FarmExpense;

class FarmExpenseResource extends Resource
{
    protected static ?string $model = FarmExpense::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                Grid::make(2)->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->required(),
                    Select::make('crop_cycle_id')
                        ->label('Crop Cycle')
                        ->relationship('cropCycle', 'crop_name')
                        ->searchable()
                        ->nullable(),
                ]),
                Grid::make(3)->schema([
                    DatePicker::make('expense_date')->required(),
                    Select::make('category')
                        ->options(array_combine(FarmExpense::CATEGORIES, array_map('ucfirst', FarmExpense::CATEGORIES)))
                        ->required(),
                    TextInput::make('amount')->required()->numeric()->prefix('GHS')->step(0.01),
                ]),
                TextInput::make('description')->required()->maxLength(255)->columnSpanFull(),
                TextInput::make('supplier')->maxLength(255),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('farm.name')->label('Farm')->searchable()->sortable(),
                TextColumn::make('expense_date')->date()->sortable(),
                TextColumn::make('category')->badge(),
                TextColumn::make('description')->limit(40),
                TextColumn::make('amount')->money('GHS')->sortable(),
                TextColumn::make('supplier'),
                TextColumn::make('cropCycle.crop_name')->label('Crop Cycle'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options(array_combine(FarmExpense::CATEGORIES, array_map('ucfirst', FarmExpense::CATEGORIES))),
                Tables\Filters\SelectFilter::make('farm_id')
                    ->label('Farm')
                    ->relationship('farm', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ])
            ->defaultSort('expense_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmExpenses::route('/'),
            'create' => Pages\CreateFarmExpense::route('/create'),
            'edit'   => Pages\EditFarmExpense::route('/{record}/edit'),
        ];
    }
}
