<?php

namespace Modules\Requisition\Filament\Resources\RequisitionGrnResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GrnLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $title = 'Receipt Lines';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Line Details')->schema([
                Grid::make(2)->schema([
                    TextInput::make('description')->required()->maxLength(255),
                    TextInput::make('unit')->default('pcs')->required(),
                ]),
                Grid::make(3)->schema([
                    TextInput::make('quantity_ordered')->label('Ordered Qty')->numeric()->required(),
                    TextInput::make('quantity_received')->label('Received Qty')->numeric()->required(),
                    TextInput::make('quantity_rejected')->label('Rejected Qty')->numeric()->default(0),
                ]),
                TextInput::make('rejection_reason')->label('Rejection Reason')->nullable()->maxLength(255),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')->limit(40),
                TextColumn::make('unit'),
                TextColumn::make('quantity_ordered')->label('Ordered'),
                TextColumn::make('quantity_received')->label('Received'),
                TextColumn::make('quantity_accepted')->label('Accepted'),
                TextColumn::make('quantity_rejected')->label('Rejected'),
                TextColumn::make('rejection_reason')->label('Rejection Reason')->placeholder('—')->limit(30),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}