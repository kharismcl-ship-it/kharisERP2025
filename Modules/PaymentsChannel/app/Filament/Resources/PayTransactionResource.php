<?php

namespace Modules\PaymentsChannel\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\PaymentsChannel\Filament\Resources\PayTransactionResource\Pages;
use Modules\PaymentsChannel\Models\PayTransaction;

class PayTransactionResource extends Resource
{
    protected static ?string $model = PayTransaction::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|\UnitEnum|null $navigationGroup = 'Payments';

    protected static string $title = 'Payment Transactions';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('pay_intent_id')
                    ->relationship('payIntent', 'reference')
                    ->required(),
                Forms\Components\Select::make('company_id')
                    ->relationship('company', 'name')
                    ->required(),
                Forms\Components\Select::make('provider')
                    ->options([
                        'flutterwave' => 'Flutterwave',
                        'paystack' => 'Paystack',
                        'payswitch' => 'PaySwitch',
                        'stripe' => 'Stripe',
                        'ghanapay' => 'GhanaPay',
                        'manual' => 'Manual',
                    ])
                    ->required(),
                Forms\Components\Select::make('transaction_type')
                    ->options([
                        'payment' => 'Payment',
                        'refund' => 'Refund',
                        'fee' => 'Fee',
                        'payout' => 'Payout',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('currency')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('provider_transaction_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'successful' => 'Successful',
                        'failed' => 'Failed',
                    ])
                    ->required(),
                Forms\Components\KeyValue::make('raw_payload')
                    ->columnSpan('full'),
                Forms\Components\DateTimePicker::make('processed_at'),
                Forms\Components\Textarea::make('error_message')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payIntent.reference')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('provider')
                    ->searchable(),
                Tables\Columns\TextColumn::make('transaction_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->money('ghs')
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('provider_transaction_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('processed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provider'),
                Tables\Filters\SelectFilter::make('transaction_type'),
                Tables\Filters\SelectFilter::make('status'),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayTransactions::route('/'),
            'create' => Pages\CreatePayTransaction::route('/create'),
            'edit' => Pages\EditPayTransaction::route('/{record}/edit'),
        ];
    }
}
