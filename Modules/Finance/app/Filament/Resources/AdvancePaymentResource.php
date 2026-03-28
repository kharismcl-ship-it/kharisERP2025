<?php

namespace Modules\Finance\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Finance\Filament\Resources\AdvancePaymentResource\Pages;
use Modules\Finance\Models\AdvancePayment;

class AdvancePaymentResource extends Resource
{
    protected static ?string $model = AdvancePayment::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownCircle;

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 26;

    protected static ?string $navigationLabel = 'Advance Payments';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Advance Payment Details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('company_id')
                        ->relationship('company', 'name')
                        ->required()
                        ->searchable()
                        ->columnSpanFull(),

                    Forms\Components\Select::make('advance_type')
                        ->label('Advance Type')
                        ->options([
                            'customer_deposit' => 'Customer Deposit',
                            'vendor_advance'   => 'Vendor Advance',
                        ])
                        ->default('customer_deposit')
                        ->required()
                        ->live(),

                    Forms\Components\TextInput::make('party_name')
                        ->label('Party Name')
                        ->helperText('Customer or vendor name')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('amount')
                        ->numeric()
                        ->prefix('GHS')
                        ->required()
                        ->minValue(0.01),

                    Forms\Components\Select::make('currency')
                        ->options(['GHS' => 'GHS', 'USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP'])
                        ->default('GHS')
                        ->required(),

                    Forms\Components\DatePicker::make('received_date')
                        ->label('Received Date')
                        ->required()
                        ->default(now()),

                    Forms\Components\Select::make('payment_method')
                        ->options([
                            'cash'       => 'Cash',
                            'bank'       => 'Bank Transfer',
                            'momo'       => 'Mobile Money',
                            'card'       => 'Card',
                            'cheque'     => 'Cheque',
                        ])
                        ->nullable(),

                    Forms\Components\TextInput::make('reference')
                        ->nullable()
                        ->maxLength(255),

                    Forms\Components\Select::make('gl_account_id')
                        ->label('GL Account')
                        ->relationship('glAccount', 'name')
                        ->searchable()
                        ->nullable()
                        ->placeholder('Select account'),

                    Forms\Components\Textarea::make('notes')
                        ->nullable()
                        ->rows(2)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('advance_number')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('advance_type')
                    ->badge()
                    ->color(fn (string $state) => $state === 'customer_deposit' ? 'info' : 'warning')
                    ->formatStateUsing(fn (string $state) => $state === 'customer_deposit' ? 'Customer Deposit' : 'Vendor Advance'),

                Tables\Columns\TextColumn::make('party_name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->money('GHS')
                    ->sortable(),

                Tables\Columns\TextColumn::make('applied_amount')
                    ->label('Applied')
                    ->money('GHS')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'open'              => 'warning',
                        'partially_applied' => 'info',
                        'fully_applied'     => 'success',
                        default             => 'gray',
                    }),

                Tables\Columns\TextColumn::make('received_date')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('received_date', 'desc')
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAdvancePayments::route('/'),
            'create' => Pages\CreateAdvancePayment::route('/create'),
            'edit'   => Pages\EditAdvancePayment::route('/{record}/edit'),
        ];
    }
}