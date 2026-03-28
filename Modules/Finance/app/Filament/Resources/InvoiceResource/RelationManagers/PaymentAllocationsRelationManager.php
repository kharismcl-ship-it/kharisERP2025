<?php

namespace Modules\Finance\Filament\Resources\InvoiceResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentAllocationsRelationManager extends RelationManager
{
    protected static string $relationship = 'paymentAllocations';

    protected static ?string $title = 'Payment Allocations';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('payment_id')
                ->label('Payment')
                ->relationship('payment', 'id')
                ->required()
                ->searchable(),

            Forms\Components\TextInput::make('amount')
                ->numeric()
                ->prefix('GHS')
                ->required()
                ->minValue(0.01),

            Forms\Components\DateTimePicker::make('allocated_at')
                ->label('Allocated At')
                ->default(now())
                ->required(),

            Forms\Components\Textarea::make('notes')
                ->nullable()
                ->rows(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment.id')
                    ->label('Payment #')
                    ->formatStateUsing(fn ($state) => "#{$state}"),
                Tables\Columns\TextColumn::make('amount')->money('GHS'),
                Tables\Columns\TextColumn::make('allocated_at')->dateTime()->label('Date'),
                Tables\Columns\TextColumn::make('notes')->limit(40)->placeholder('—'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}