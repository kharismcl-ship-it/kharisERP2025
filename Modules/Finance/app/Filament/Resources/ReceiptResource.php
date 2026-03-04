<?php

namespace Modules\Finance\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Finance\Filament\Resources\ReceiptResource\Pages;
use Modules\Finance\Models\Receipt;

class ReceiptResource extends Resource
{
    protected static ?string $model = Receipt::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Info')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('customer_name')
                            
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_phone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_type')
                            ->maxLength(255)
                            ->placeholder('e.g. student, resident'),
                        Forms\Components\TextInput::make('customer_id')
                            ->label('Customer Record ID')
                            ->numeric(),
                    ]),

                Section::make('Receipt Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('invoice_id')
                            ->relationship('invoice', 'invoice_number')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('payment_id')
                            ->relationship('payment', 'reference')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('receipt_number')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('receipt_date')
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('GHS'),
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
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Section::make('Status Tracking')
                    ->columns(1)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft'      => 'Draft',
                                'sent'       => 'Sent',
                                'viewed'     => 'Viewed',
                                'downloaded' => 'Downloaded',
                            ])
                            ->required()
                            ->default('draft'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('receipt_number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('GHS')
                    ->sortable(),
                Tables\Columns\TextColumn::make('receipt_date')
                    ->date()
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'draft'      => 'gray',
                        'sent'       => 'info',
                        'viewed'     => 'warning',
                        'downloaded' => 'success',
                        default      => 'gray',
                    }),
                Tables\Columns\TextColumn::make('invoice.invoice_number')
                    ->label('Invoice')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'      => 'Draft',
                        'sent'       => 'Sent',
                        'viewed'     => 'Viewed',
                        'downloaded' => 'Downloaded',
                    ]),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cash'   => 'Cash',
                        'bank'   => 'Bank Transfer',
                        'momo'   => 'Mobile Money',
                        'card'   => 'Card',
                        'cheque' => 'Cheque',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListReceipts::route('/'),
            'create' => Pages\CreateReceipt::route('/create'),
            'view'   => Pages\ViewReceipt::route('/{record}'),
            'edit'   => Pages\EditReceipt::route('/{record}/edit'),
        ];
    }
}