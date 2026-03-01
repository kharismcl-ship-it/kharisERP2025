<?php

namespace Modules\Construction\Filament\Resources\ConstructionProjectResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Modules\Construction\Models\ProjectBudgetItem;

class ProjectBudgetItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'budgetItems';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('category')
                ->options(array_combine(ProjectBudgetItem::CATEGORIES, array_map('ucfirst', ProjectBudgetItem::CATEGORIES)))
                ->required(),
            TextInput::make('description')->required()->maxLength(255)->columnSpanFull(),
            TextInput::make('budgeted_amount')->label('Budgeted')->numeric()->prefix('GHS')->step(0.01),
            TextInput::make('actual_amount')->label('Actual')->numeric()->prefix('GHS')->step(0.01),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category')->badge(),
                TextColumn::make('description'),
                TextColumn::make('budgeted_amount')->label('Budgeted')->money('GHS'),
                TextColumn::make('actual_amount')->label('Actual')->money('GHS'),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
}
