<?php

namespace Modules\Finance\Filament\Resources\BudgetResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class BudgetLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $title = 'Budget Lines';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('account_id')
                    ->relationship('account', 'name')
                    ->searchable()
                    ->required()
                    ->label('Account'),
                Forms\Components\Select::make('cost_centre_id')
                    ->relationship('costCentre', 'name')
                    ->searchable()
                    ->label('Cost Centre'),
                Forms\Components\TextInput::make('description')->maxLength(255),
                Forms\Components\TextInput::make('jan')->numeric()->prefix('GHS')->default(0),
                Forms\Components\TextInput::make('feb')->numeric()->prefix('GHS')->default(0),
                Forms\Components\TextInput::make('mar')->numeric()->prefix('GHS')->default(0),
                Forms\Components\TextInput::make('apr')->numeric()->prefix('GHS')->default(0),
                Forms\Components\TextInput::make('may')->numeric()->prefix('GHS')->default(0),
                Forms\Components\TextInput::make('jun')->numeric()->prefix('GHS')->default(0),
                Forms\Components\TextInput::make('jul')->numeric()->prefix('GHS')->default(0),
                Forms\Components\TextInput::make('aug')->numeric()->prefix('GHS')->default(0),
                Forms\Components\TextInput::make('sep')->numeric()->prefix('GHS')->default(0),
                Forms\Components\TextInput::make('oct')->numeric()->prefix('GHS')->default(0),
                Forms\Components\TextInput::make('nov')->numeric()->prefix('GHS')->default(0),
                Forms\Components\TextInput::make('dec')->numeric()->prefix('GHS')->default(0),
                Forms\Components\TextInput::make('annual_total')
                    ->numeric()
                    ->prefix('GHS')
                    ->disabled()
                    ->label('Annual Total (auto-computed)'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('account.name')->label('Account')->searchable(),
                Tables\Columns\TextColumn::make('costCentre.name')->label('Cost Centre')->placeholder('—'),
                Tables\Columns\TextColumn::make('jan')->money('GHS'),
                Tables\Columns\TextColumn::make('feb')->money('GHS'),
                Tables\Columns\TextColumn::make('mar')->money('GHS'),
                Tables\Columns\TextColumn::make('annual_total')->money('GHS')->weight('bold'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}