<?php

namespace Modules\Hostels\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Hostels\Filament\Resources\DepositResource\Pages;
use Modules\Hostels\Models\Deposit;

class DepositResource extends Resource
{
    protected static ?string $model = Deposit::class;

    protected static ?string $slug = 'deposits';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedLockClosed;

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('hostel_occupant_id')
                    ->relationship('occupant', 'full_name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Select::make('booking_id')
                    ->relationship('booking', 'id')
                    ->searchable()
                    ->preload(),

                Select::make('hostel_id')
                    ->relationship('hostel', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                TextInput::make('amount')
                    ->numeric()
                    ->step(0.01)
                    ->required(),

                Select::make('deposit_type')
                    ->options([
                        'security' => 'Security Deposit',
                        'advance' => 'Advance Payment',
                        'damage' => 'Damage Deposit',
                        'utility' => 'Utility Deposit',
                    ])
                    ->default('security')
                    ->required(),

                Select::make('status')
                    ->options([
                        Deposit::STATUS_PENDING => 'Pending',
                        Deposit::STATUS_COLLECTED => 'Collected',
                        Deposit::STATUS_REFUNDED => 'Refunded',
                        Deposit::STATUS_PARTIAL_REFUND => 'Partial Refund',
                        Deposit::STATUS_FORFEITED => 'Forfeited',
                    ])
                    ->default(Deposit::STATUS_PENDING)
                    ->required(),

                DatePicker::make('collected_date'),

                DatePicker::make('refunded_date'),

                TextInput::make('refund_amount')
                    ->numeric()
                    ->step(0.01)
                    ->default(0),

                TextInput::make('deductions')
                    ->numeric()
                    ->step(0.01)
                    ->default(0),

                Textarea::make('deduction_reason')
                    ->rows(3),

                Select::make('invoice_id')
                    ->relationship('invoice', 'invoice_number')
                    ->searchable()
                    ->preload(),

                Select::make('journal_entry_id')
                    ->relationship('journalEntry', 'reference')
                    ->searchable()
                    ->preload(),

                Textarea::make('notes')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('occupant.full_name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('hostel.name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('booking.id')
                    ->label('Booking ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('amount')
                    ->money('GHS')
                    ->sortable(),

                BadgeColumn::make('deposit_type')
                    ->colors([
                        'primary' => 'security',
                        'success' => 'advance',
                        'warning' => 'damage',
                        'danger' => 'utility',
                    ]),

                BadgeColumn::make('status')
                    ->colors([
                        'primary' => Deposit::STATUS_PENDING,
                        'success' => Deposit::STATUS_COLLECTED,
                        'warning' => Deposit::STATUS_REFUNDED,
                        'danger' => Deposit::STATUS_PARTIAL_REFUND,
                        'gray' => Deposit::STATUS_FORFEITED,
                    ]),

                TextColumn::make('collected_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('refunded_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('refund_amount')
                    ->money('GHS')
                    ->sortable(),

                TextColumn::make('deductions')
                    ->money('GHS')
                    ->sortable(),

                TextColumn::make('invoice.invoice_number')
                    ->label('Invoice')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                // Filters can be added here
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeposits::route('/'),
            'create' => Pages\CreateDeposit::route('/create'),
            'edit' => Pages\EditDeposit::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['occupant', 'hostel', 'booking', 'invoice', 'journalEntry']);
    }
}
