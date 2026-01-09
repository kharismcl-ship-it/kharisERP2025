<?php

namespace Modules\Hostels\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Hostels\Filament\Resources\HostelUtilityChargeResource\Pages;
use Modules\Hostels\Models\HostelUtilityCharge;

class HostelUtilityChargeResource extends Resource
{
    protected static ?string $model = HostelUtilityCharge::class;

    protected static ?string $slug = 'hostel-utility-charges';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('hostel_id')
                    ->relationship('hostel', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Select::make('room_id')
                    ->relationship('room', 'room_number')
                    ->searchable()
                    ->preload(),

                Select::make('hostel_occupant_id')
                    ->relationship('occupant', 'full_name')
                    ->searchable()
                    ->preload(),

                Select::make('utility_type')
                    ->options([
                        'electricity' => 'Electricity',
                        'water' => 'Water',
                        'internet' => 'Internet',
                        'gas' => 'Gas',
                        'maintenance' => 'Maintenance',
                        'service' => 'Service',
                    ])
                    ->required(),

                TextInput::make('meter_number'),

                TextInput::make('previous_reading')
                    ->numeric()
                    ->step(0.01),

                TextInput::make('current_reading')
                    ->numeric()
                    ->step(0.01),

                TextInput::make('consumption')
                    ->numeric()
                    ->step(0.01)
                    ->readOnly(),

                TextInput::make('rate_per_unit')
                    ->numeric()
                    ->step(0.0001)
                    ->default(0),

                TextInput::make('fixed_charge')
                    ->numeric()
                    ->step(0.01)
                    ->default(0),

                TextInput::make('total_amount')
                    ->numeric()
                    ->step(0.01)
                    ->readOnly(),

                DatePicker::make('billing_period_start')
                    ->required(),

                DatePicker::make('billing_period_end')
                    ->required(),

                DatePicker::make('due_date')
                    ->required(),

                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'billed' => 'Billed',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('pending'),

                Select::make('billing_cycle_id')
                    ->relationship('billingCycle', 'name')
                    ->searchable()
                    ->preload(),

                Toggle::make('auto_calculate')
                    ->default(true)
                    ->reactive(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hostel.name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('room.room_number')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('occupant.full_name')
                    ->sortable()
                    ->searchable(),

                BadgeColumn::make('utility_type')
                    ->colors([
                        'primary' => 'electricity',
                        'success' => 'water',
                        'warning' => 'internet',
                        'danger' => 'gas',
                        'gray' => 'maintenance',
                        'info' => 'service',
                    ]),

                TextColumn::make('consumption')
                    ->numeric(decimalPlaces: 2),

                TextColumn::make('total_amount')
                    ->money('GHS')
                    ->sortable(),

                TextColumn::make('billing_period_start')
                    ->date()
                    ->sortable(),

                TextColumn::make('billing_period_end')
                    ->date()
                    ->sortable(),

                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'pending',
                        'success' => 'billed',
                        'warning' => 'paid',
                        'danger' => 'overdue',
                        'gray' => 'cancelled',
                    ]),

                IconColumn::make('billingCycle.name')
                    ->label('Billing Cycle')
                    ->icon(fn ($state) => $state ? Heroicon::OutlinedCurrencyDollar : null),
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
            'index' => Pages\ListHostelUtilityCharges::route('/'),
            'create' => Pages\CreateHostelUtilityCharge::route('/create'),
            'edit' => Pages\EditHostelUtilityCharge::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['hostel', 'room', 'occupant', 'billingCycle']);
    }
}
