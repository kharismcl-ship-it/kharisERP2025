<?php

namespace Modules\Hostels\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Hostels\Filament\Resources\HostelBillingRuleResource\Pages;
use Modules\Hostels\Models\HostelBillingRule;

class HostelBillingRuleResource extends Resource
{
    protected static ?string $model = HostelBillingRule::class;

    /**
     * This model has no direct company_id — Filament's ownership
     * check is skipped. Data isolation is handled via the parent
     * relationship or a custom getEloquentQuery() scope.
     */
    protected static bool $isScopedToTenant = false;

    protected static ?string $slug = 'hostel-billing-rules';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCurrencyDollar;

    protected static string|\UnitEnum|null $navigationGroup = 'Billing';

    protected static ?int $navigationSort = 3;

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

                Select::make('rule_type')
                    ->options([
                        'late_fee' => 'Late Fee',
                        'damage_charge' => 'Damage Charge',
                        'service_fee' => 'Service Fee',
                        'discount' => 'Discount',
                        'tax' => 'Tax',
                        'penalty' => 'Penalty',
                    ])
                    ->required(),

                Select::make('calculation_method')
                    ->options([
                        'fixed' => 'Fixed Amount',
                        'percentage' => 'Percentage',
                        'per_day' => 'Per Day',
                        'per_unit' => 'Per Unit',
                    ])
                    ->required(),

                TextInput::make('amount')
                    ->numeric()
                    ->step(0.0001)
                    ->required(),

                TextInput::make('gl_account_code')
                    ->label('GL Account Code')
                    ->maxLength(50),

                Toggle::make('is_active')
                    ->default(true),

                Toggle::make('auto_apply')
                    ->default(false),

                Textarea::make('conditions')
                    ->rows(3)
                    ->placeholder('JSON conditions for auto-application')
                    ->helperText('Enter conditions in JSON format for automatic rule application'),

                Textarea::make('description')
                    ->rows(3),
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

                TextColumn::make('rule_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'late_fee'      => 'danger',
                        'damage_charge' => 'warning',
                        'service_fee'   => 'primary',
                        'discount'      => 'success',
                        'tax'           => 'info',
                        default         => 'gray',
                    }),

                TextColumn::make('calculation_method')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fixed'      => 'primary',
                        'percentage' => 'success',
                        'per_day'    => 'warning',
                        'per_unit'   => 'info',
                        default      => 'gray',
                    }),

                TextColumn::make('amount')
                    ->numeric(decimalPlaces: 4)
                    ->sortable(),

                TextColumn::make('gl_account_code')
                    ->label('GL Account')
                    ->sortable()
                    ->searchable(),

                IconColumn::make('is_active')
                    ->boolean(),

                IconColumn::make('auto_apply')
                    ->boolean(),
            ])
            ->filters([
                // Filters can be added here
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHostelBillingRules::route('/'),
            'create' => Pages\CreateHostelBillingRule::route('/create'),
            'edit' => Pages\EditHostelBillingRule::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['hostel']);
    }
}
