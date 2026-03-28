<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\VendorStatementResource\Pages;
use Modules\ProcurementInventory\Filament\Resources\VendorStatementResource\RelationManagers\StatementLinesRelationManager;
use Modules\ProcurementInventory\Models\PurchaseOrder;
use Modules\ProcurementInventory\Models\VendorStatement;

class VendorStatementResource extends Resource
{
    protected static ?string $model = VendorStatement::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentMagnifyingGlass;

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?int $navigationSort = 28;

    protected static ?string $navigationLabel = 'Vendor Statements';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Statement Details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('company_id')
                        ->relationship('company', 'name')
                        ->required()
                        ->searchable()
                        ->columnSpanFull(),

                    Forms\Components\Select::make('vendor_id')
                        ->relationship('vendor', 'name')
                        ->required()
                        ->searchable()
                        ->label('Vendor'),

                    Forms\Components\DatePicker::make('statement_date')
                        ->required()
                        ->default(now()),

                    Forms\Components\TextInput::make('statement_reference')
                        ->nullable()
                        ->maxLength(255)
                        ->label('Vendor Statement Ref'),

                    Forms\Components\DatePicker::make('period_from')
                        ->nullable()
                        ->label('Period From'),

                    Forms\Components\DatePicker::make('period_to')
                        ->nullable()
                        ->label('Period To'),

                    Forms\Components\TextInput::make('opening_balance')
                        ->numeric()
                        ->prefix('GHS')
                        ->default(0),

                    Forms\Components\TextInput::make('closing_balance')
                        ->numeric()
                        ->prefix('GHS')
                        ->default(0),

                    Forms\Components\TextInput::make('total_invoiced')
                        ->numeric()
                        ->prefix('GHS')
                        ->default(0),

                    Forms\Components\TextInput::make('total_paid')
                        ->numeric()
                        ->prefix('GHS')
                        ->default(0),

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
                Tables\Columns\TextColumn::make('vendor.name')
                    ->label('Vendor')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('statement_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('period_from')
                    ->date()
                    ->label('From')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('period_to')
                    ->date()
                    ->label('To')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('opening_balance')
                    ->money('GHS')
                    ->label('Opening'),

                Tables\Columns\TextColumn::make('closing_balance')
                    ->money('GHS')
                    ->label('Closing'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'uploaded'   => 'info',
                        'reconciled' => 'success',
                        'disputed'   => 'danger',
                        default      => 'gray',
                    }),
            ])
            ->defaultSort('statement_date', 'desc')
            ->actions([
                Tables\Actions\Action::make('auto_match')
                    ->label('Auto-Match')
                    ->icon(Heroicon::OutlinedArrowsRightLeft)
                    ->color('warning')
                    ->visible(fn (VendorStatement $record) => $record->status === 'uploaded')
                    ->action(function (VendorStatement $record): void {
                        $matched = 0;
                        foreach ($record->lines as $line) {
                            if ($line->reference) {
                                $po = PurchaseOrder::where('po_number', $line->reference)
                                    ->where('vendor_id', $record->vendor_id)
                                    ->first();
                                if ($po) {
                                    $line->update([
                                        'matched_po_id' => $po->id,
                                        'match_status'  => 'matched',
                                    ]);
                                    $matched++;
                                }
                            }
                        }

                        // Check if balances reconcile within 1%
                        $calculatedClosing = $record->opening_balance
                            + $record->lines()->where('transaction_type', 'invoice')->sum('amount')
                            - $record->lines()->where('transaction_type', 'payment')->sum('amount');

                        $tolerance = abs((float) $record->closing_balance) * 0.01;
                        $diff      = abs($calculatedClosing - (float) $record->closing_balance);

                        if ($diff <= $tolerance) {
                            $record->update(['status' => 'reconciled']);
                            Notification::make()->success()
                                ->title("Statement reconciled. {$matched} lines matched.")
                                ->send();
                        } else {
                            Notification::make()->warning()
                                ->title("{$matched} lines matched. Balance difference: GHS " . number_format($diff, 2) . " — review required.")
                                ->send();
                        }
                    }),

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
            StatementLinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVendorStatements::route('/'),
            'create' => Pages\CreateVendorStatement::route('/create'),
            'edit'   => Pages\EditVendorStatement::route('/{record}/edit'),
            'view'   => Pages\ViewVendorStatement::route('/{record}'),
        ];
    }
}