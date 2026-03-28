<?php

namespace Modules\ProcurementInventory\Filament\Resources\VendorStatementResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class StatementLinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $title = 'Statement Lines';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\DatePicker::make('transaction_date')
                ->required()
                ->default(now()),

            Forms\Components\Select::make('transaction_type')
                ->options([
                    'invoice'    => 'Invoice',
                    'payment'    => 'Payment',
                    'credit'     => 'Credit Note',
                    'debit_note' => 'Debit Note',
                    'opening'    => 'Opening Balance',
                ])
                ->required(),

            Forms\Components\TextInput::make('reference')
                ->nullable()
                ->maxLength(255)
                ->label("Vendor's Reference"),

            Forms\Components\TextInput::make('description')
                ->nullable()
                ->maxLength(255),

            Forms\Components\TextInput::make('amount')
                ->numeric()
                ->prefix('GHS')
                ->required()
                ->helperText('Positive = charge; negative = credit'),

            Forms\Components\Select::make('match_status')
                ->options([
                    'matched'   => 'Matched',
                    'unmatched' => 'Unmatched',
                    'disputed'  => 'Disputed',
                ])
                ->default('unmatched'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')->date()->sortable()->label('Date'),
                Tables\Columns\TextColumn::make('transaction_type')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'invoice'    => 'danger',
                        'payment'    => 'success',
                        'credit'     => 'info',
                        'debit_note' => 'warning',
                        'opening'    => 'gray',
                        default      => 'gray',
                    })
                    ->label('Type'),
                Tables\Columns\TextColumn::make('reference')->placeholder('—')->label("Ref"),
                Tables\Columns\TextColumn::make('description')->limit(40)->placeholder('—'),
                Tables\Columns\TextColumn::make('amount')
                    ->money('GHS')
                    ->color(fn ($record) => (float) $record->amount >= 0 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('match_status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'matched'   => 'success',
                        'unmatched' => 'warning',
                        'disputed'  => 'danger',
                        default     => 'gray',
                    })
                    ->label('Match'),
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