<?php

namespace Modules\Hostels\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Hostels\Filament\Resources\PricingPolicyResource\Pages;
use Modules\Hostels\Models\PricingPolicy;

class PricingPolicyResource extends Resource
{
    protected static ?string $model = PricingPolicy::class;

    protected static ?string $slug = 'pricing-policies';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Policy Details')
                    ->components([
                        Select::make('hostel_id')
                            ->label('Hostel')
                            ->relationship('hostel', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('name')
                            ->label('Policy Name')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Description')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ]),

                Section::make('Pricing Configuration')
                    ->components([
                        Select::make('policy_type')
                            ->label('Policy Type')
                            ->options([
                                'seasonal' => 'Seasonal Pricing',
                                'demand' => 'Demand-Based Pricing',
                                'length_of_stay' => 'Length of Stay Discount',
                                'special_event' => 'Special Event Pricing',
                            ])
                            ->required()
                            ->reactive(),
                        Select::make('adjustment_type')
                            ->label('Adjustment Type')
                            ->options([
                                'percentage' => 'Percentage',
                                'fixed_amount' => 'Fixed Amount',
                            ])
                            ->required()
                            ->reactive(),
                        TextInput::make('adjustment_value')
                            ->label('Adjustment Value')
                            ->numeric()
                            ->required()
                            ->suffix(fn ($get) => $get('adjustment_type') === 'percentage' ? '%' : 'GHS')
                            ->rules([
                                function ($get) {
                                    return function ($attribute, $value, $fail) use ($get) {
                                        if ($get('adjustment_type') === 'percentage' && $value > 100) {
                                            $fail('Percentage cannot exceed 100%');
                                        }
                                    };
                                },
                            ]),
                    ]),

                Section::make('Application Conditions')
                    ->components([
                        DatePicker::make('valid_from')
                            ->label('Valid From')
                            ->nullable(),
                        DatePicker::make('valid_to')
                            ->label('Valid To')
                            ->nullable(),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        KeyValue::make('conditions')
                            ->label('Additional Conditions')
                            ->keyLabel('Condition Type')
                            ->valueLabel('Value')
                            ->addable(false)
                            ->editableKeys(false)
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hostel.name')
                    ->label('Hostel')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Policy Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('policy_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'seasonal' => 'success',
                        'demand' => 'warning',
                        'length_of_stay' => 'info',
                        'special_event' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('adjustment_type')
                    ->label('Adjustment')
                    ->formatStateUsing(fn ($state, $record) => $record->adjustment_type === 'percentage'
                            ? "+{$record->adjustment_value}%"
                            : "+GHS {$record->adjustment_value}"
                    )
                    ->badge()
                    ->color(fn ($record) => $record->adjustment_value >= 0 ? 'danger' : 'success'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('valid_from')
                    ->label('Valid From')
                    ->date()
                    ->sortable(),
                TextColumn::make('valid_to')
                    ->label('Valid To')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('hostel_id')
                    ->label('Hostel')
                    ->relationship('hostel', 'name'),
                SelectFilter::make('policy_type')
                    ->label('Policy Type')
                    ->options([
                        'seasonal' => 'Seasonal',
                        'demand' => 'Demand-Based',
                        'length_of_stay' => 'Length of Stay',
                        'special_event' => 'Special Event',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Active Status'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPricingPolicies::route('/'),
            'create' => Pages\CreatePricingPolicy::route('/create'),
            'edit' => Pages\EditPricingPolicy::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('hostel');
    }
}
