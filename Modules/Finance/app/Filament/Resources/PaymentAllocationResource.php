<?php

namespace Modules\Finance\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Finance\Filament\Resources\PaymentAllocationResource\Pages;
use Modules\Finance\Models\Invoice;
use Modules\Finance\Models\Payment;
use Modules\Finance\Models\PaymentAllocation;

class PaymentAllocationResource extends Resource
{
    protected static ?string $model = PaymentAllocation::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 25;

    protected static ?string $navigationLabel = 'Payment Allocations';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Allocation Details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('company_id')
                        ->relationship('company', 'name')
                        ->required()
                        ->searchable()
                        ->columnSpanFull(),

                    Forms\Components\Select::make('payment_id')
                        ->label('Payment')
                        ->options(fn () => Payment::query()
                            ->with('invoice')
                            ->get()
                            ->mapWithKeys(fn (Payment $p) => [
                                $p->id => "#{$p->id} — GHS " . number_format((float) $p->amount, 2) . " ({$p->payment_date?->format('d/m/Y')})",
                            ]))
                        ->required()
                        ->searchable(),

                    Forms\Components\Select::make('invoice_id')
                        ->label('Invoice')
                        ->options(fn () => Invoice::query()
                            ->whereIn('status', ['sent', 'overdue', 'partial'])
                            ->get()
                            ->mapWithKeys(fn (Invoice $inv) => [
                                $inv->id => "{$inv->invoice_number} — GHS " . number_format((float) $inv->total, 2) . " (outstanding: GHS " . number_format($inv->amountOutstanding(), 2) . ")",
                            ]))
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
                        ->rows(2)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment.id')
                    ->label('Payment #')
                    ->formatStateUsing(fn ($state) => "#{$state}")
                    ->sortable(),

                Tables\Columns\TextColumn::make('invoice.invoice_number')
                    ->label('Invoice')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->money('GHS')
                    ->sortable(),

                Tables\Columns\TextColumn::make('allocated_at')
                    ->label('Allocated At')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->limit(40)
                    ->placeholder('—'),
            ])
            ->defaultSort('allocated_at', 'desc')
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
            'index'  => Pages\ListPaymentAllocations::route('/'),
            'create' => Pages\CreatePaymentAllocation::route('/create'),
        ];
    }
}