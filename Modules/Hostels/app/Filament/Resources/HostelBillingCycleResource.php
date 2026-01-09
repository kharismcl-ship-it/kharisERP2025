<?php

namespace Modules\Hostels\Filament\Resources;

use BackedEnum;
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
use Modules\Hostels\Filament\Resources\HostelBillingCycleResource\Pages;
use Modules\Hostels\Models\HostelBillingCycle;

class HostelBillingCycleResource extends Resource
{
    protected static ?string $model = HostelBillingCycle::class;

    protected static ?string $slug = 'hostel-billing-cycles';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('hostel_id')
                    ->relationship('hostel', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Select::make('cycle_type')
                    ->options([
                        'monthly' => 'Monthly',
                        'quarterly' => 'Quarterly',
                        'semester' => 'Semester',
                        'custom' => 'Custom',
                    ])
                    ->required(),

                DatePicker::make('start_date')
                    ->required(),

                DatePicker::make('end_date')
                    ->required(),

                DatePicker::make('billing_date')
                    ->required(),

                DatePicker::make('due_date')
                    ->required(),

                Toggle::make('is_active')
                    ->default(true),

                Toggle::make('auto_generate')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hostel.name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                BadgeColumn::make('cycle_type')
                    ->colors([
                        'primary' => 'monthly',
                        'success' => 'quarterly',
                        'warning' => 'semester',
                        'gray' => 'custom',
                    ]),

                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->boolean(),

                IconColumn::make('auto_generate')
                    ->boolean(),
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
            'index' => Pages\ListHostelBillingCycles::route('/'),
            'create' => Pages\CreateHostelBillingCycle::route('/create'),
            'edit' => Pages\EditHostelBillingCycle::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('hostel');
    }
}
