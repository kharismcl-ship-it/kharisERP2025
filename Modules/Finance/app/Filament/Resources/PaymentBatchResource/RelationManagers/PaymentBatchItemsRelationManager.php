<?php

namespace Modules\Finance\Filament\Resources\PaymentBatchResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Finance\Models\Invoice;

class PaymentBatchItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Batch Items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('invoice_id')
                    ->label('Invoice')
                    ->options(fn () => Invoice::where('type', 'vendor')->whereIn('status', ['sent', 'overdue'])->pluck('invoice_number', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->prefix('GHS')
                    ->required(),
                Forms\Components\TextInput::make('reference')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice.invoice_number')->label('Invoice'),
                Tables\Columns\TextColumn::make('amount')->money('GHS'),
                Tables\Columns\TextColumn::make('reference')->placeholder('—'),
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