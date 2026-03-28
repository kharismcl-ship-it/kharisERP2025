<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Modules\Farms\Models\FarmInputCreditAccount;
use Modules\Farms\Models\Farm;
use Filament\Facades\Filament;

class FarmInputCreditResource extends Resource
{
    protected static ?string $model = FarmInputCreditAccount::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';
    protected static string|\UnitEnum|null $navigationGroup = 'Cooperatives';
    protected static ?string $navigationLabel = 'Input Credit Accounts';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        $companyId = Filament::getTenant()?->id;

        return $schema->components([
            Section::make('Credit Account Details')->schema([
                Grid::make(2)->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->options(fn () => Farm::where('company_id', $companyId)->pluck('name', 'id'))
                        ->required()
                        ->searchable(),
                    TextInput::make('farmer_name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('farmer_phone')
                        ->tel()
                        ->maxLength(30),
                    TextInput::make('scheme_name')
                        ->maxLength(255),
                    Select::make('scheme_type')
                        ->options([
                            'government_subsidy'  => 'Government Subsidy',
                            'cooperative_advance' => 'Cooperative Advance',
                            'commercial_credit'   => 'Commercial Credit',
                            'ngo_program'         => 'NGO Program',
                        ])
                        ->required(),
                    Select::make('status')
                        ->options([
                            'active'    => 'Active',
                            'repaid'    => 'Repaid',
                            'defaulted' => 'Defaulted',
                            'suspended' => 'Suspended',
                        ])
                        ->default('active'),
                    TextInput::make('credit_limit')
                        ->numeric()
                        ->prefix('GHS')
                        ->required(),
                    TextInput::make('amount_drawn')
                        ->numeric()
                        ->prefix('GHS')
                        ->default(0),
                    TextInput::make('amount_repaid')
                        ->numeric()
                        ->prefix('GHS')
                        ->default(0),
                    DatePicker::make('season_start'),
                    DatePicker::make('repayment_due_date'),
                ]),
                Textarea::make('notes')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account_ref')->searchable()->sortable(),
                TextColumn::make('farm.name')->label('Farm')->sortable(),
                TextColumn::make('farmer_name')->searchable(),
                TextColumn::make('scheme_name'),
                TextColumn::make('credit_limit')
                    ->money('GHS')
                    ->label('Credit Limit'),
                TextColumn::make('amount_drawn')
                    ->money('GHS')
                    ->label('Drawn'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'    => 'success',
                        'repaid'    => 'info',
                        'defaulted' => 'danger',
                        'suspended' => 'warning',
                        default     => 'gray',
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\Farms\Filament\Resources\FarmInputCreditResource\Pages\ListFarmInputCredits::route('/'),
            'create' => \Modules\Farms\Filament\Resources\FarmInputCreditResource\Pages\CreateFarmInputCredit::route('/create'),
            'edit'   => \Modules\Farms\Filament\Resources\FarmInputCreditResource\Pages\EditFarmInputCredit::route('/{record}/edit'),
        ];
    }
}