<?php

namespace Modules\Farms\Filament\Resources\FarmResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Modules\Farms\Models\FarmExpense;

class FarmExpensesRelationManager extends RelationManager
{
    protected static string $relationship = 'expenses';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('expense_date')->required(),
            Select::make('category')
                ->options(array_combine(FarmExpense::CATEGORIES, array_map('ucfirst', FarmExpense::CATEGORIES)))
                ->required(),
            TextInput::make('description')->required()->maxLength(255)->columnSpanFull(),
            TextInput::make('amount')->required()->numeric()->prefix('GHS')->step(0.01),
            TextInput::make('supplier')->maxLength(255),
            Select::make('crop_cycle_id')->label('Crop Cycle')->relationship('cropCycle', 'crop_name')->searchable()->nullable(),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('expense_date')->date()->sortable(),
                TextColumn::make('category')->badge(),
                TextColumn::make('description')->limit(40),
                TextColumn::make('amount')->money('GHS')->sortable(),
                TextColumn::make('supplier'),
                TextColumn::make('cropCycle.crop_name')->label('Crop Cycle'),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->defaultSort('expense_date', 'desc');
    }
}
