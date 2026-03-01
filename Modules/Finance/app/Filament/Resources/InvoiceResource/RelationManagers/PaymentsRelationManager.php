<?php

namespace Modules\Finance\Filament\Resources\InvoiceResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Payments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('GHS'),
                Forms\Components\DatePicker::make('payment_date')
                    ->required(),
                Forms\Components\Select::make('payment_method')
                    ->options([
                        'cash'   => 'Cash',
                        'bank'   => 'Bank Transfer',
                        'momo'   => 'Mobile Money',
                        'card'   => 'Card',
                        'cheque' => 'Cheque',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('reference')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('amount')->money('GHS')->weight('bold'),
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'cash'   => 'success',
                        'bank'   => 'info',
                        'momo'   => 'warning',
                        'card'   => 'primary',
                        'cheque' => 'gray',
                        default  => 'gray',
                    }),
                Tables\Columns\TextColumn::make('reference')->placeholder('—'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
