<?php

namespace Modules\Farms\Filament\Resources\CropCycleResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
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
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('expense_date')->date('d M Y')->sortable(),
                TextColumn::make('category')->badge(),
                TextColumn::make('description')->limit(40),
                TextColumn::make('amount')->money('GHS')->sortable(),
                TextColumn::make('supplier')->placeholder('—'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('expense_date', 'desc');
    }
}