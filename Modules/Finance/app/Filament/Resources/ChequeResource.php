<?php

namespace Modules\Finance\Filament\Resources;

use Filament\Actions\ActionGroup;
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
use Modules\Finance\Filament\Resources\ChequeResource\Pages;
use Modules\Finance\Models\Cheque;

class ChequeResource extends Resource
{
    protected static ?string $model = Cheque::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'General Ledger';

    protected static ?int $navigationSort = 49;

    protected static ?string $navigationLabel = 'Cheques';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Cheque Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('bank_account_id')
                            ->relationship('bankAccount', 'name')
                            ->required()
                            ->searchable()
                            ->label('Bank Account'),
                        Forms\Components\TextInput::make('cheque_number')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('payee_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->prefix('GHS')
                            ->required(),
                        Forms\Components\DatePicker::make('cheque_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('status')
                            ->options([
                                'issued'    => 'Issued',
                                'presented' => 'Presented',
                                'cleared'   => 'Cleared',
                                'returned'  => 'Returned',
                                'void'      => 'Void',
                            ])
                            ->default('issued'),
                        Forms\Components\Select::make('payment_id')
                            ->relationship('payment', 'id')
                            ->searchable()
                            ->label('Linked Payment')
                            ->placeholder('Optional'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cheque_number')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('payee_name')->searchable(),
                Tables\Columns\TextColumn::make('amount')->money('GHS')->sortable(),
                Tables\Columns\TextColumn::make('cheque_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('bankAccount.name')->label('Bank Account')->placeholder('—'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'issued'    => 'info',
                        'presented' => 'warning',
                        'cleared'   => 'success',
                        'returned'  => 'danger',
                        'void'      => 'danger',
                        default     => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_cleared')
                    ->label('Mark Cleared')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (Cheque $record) => in_array($record->status, ['issued', 'presented']))
                    ->action(fn (Cheque $record) => $record->update([
                        'status'       => 'cleared',
                        'cleared_date' => now(),
                    ])),
                Tables\Actions\Action::make('mark_returned')
                    ->label('Mark Returned')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn (Cheque $record) => in_array($record->status, ['issued', 'presented']))
                    ->form([
                        Forms\Components\TextInput::make('return_reason')
                            ->required()
                            ->label('Return Reason'),
                    ])
                    ->action(fn (Cheque $record, array $data) => $record->update([
                        'status'        => 'returned',
                        'return_reason' => $data['return_reason'],
                    ])),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCheques::route('/'),
            'create' => Pages\CreateCheque::route('/create'),
            'edit'   => Pages\EditCheque::route('/{record}/edit'),
        ];
    }
}