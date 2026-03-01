<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
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
use Modules\HR\Filament\Resources\AllowanceTypeResource\Pages;
use Modules\HR\Models\AllowanceType;

class AllowanceTypeResource extends Resource
{
    protected static ?string $model = AllowanceType::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPlusCircle;

    protected static string|\UnitEnum|null $navigationGroup = 'Payroll';

    protected static ?int $navigationSort = 51;

    protected static ?string $navigationLabel = 'Allowance Types';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Allowance Details')
                    ->description('Configure how this allowance is calculated and applied')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable()->preload()->required(),
                        Forms\Components\TextInput::make('name')
                            ->required()->maxLength(100),
                        Forms\Components\TextInput::make('code')
                            ->maxLength(30)
                            ->unique(ignoreRecord: true)
                            ->helperText('Short code for payroll processing (e.g. TRANS, HOUSE)'),
                        Forms\Components\Select::make('calculation_type')
                            ->options(AllowanceType::CALCULATION_TYPES)
                            ->required()
                            ->native(false)
                            ->live(),
                        Forms\Components\TextInput::make('default_amount')
                            ->numeric()->prefix('GHS')
                            ->visible(fn ($get) => $get('calculation_type') === 'fixed'),
                        Forms\Components\TextInput::make('percentage_value')
                            ->numeric()->suffix('%')
                            ->helperText('Percentage of basic salary')
                            ->visible(fn ($get) => $get('calculation_type') === 'percentage'),
                        Forms\Components\TextInput::make('gl_account_code')
                            ->label('GL Account Code')
                            ->maxLength(30),
                    ]),

                Section::make('Flags')
                    ->collapsible()
                    ->columns(3)
                    ->schema([
                        Forms\Components\Toggle::make('is_taxable')
                            ->label('Taxable?')
                            ->helperText('Include in PAYE calculation')
                            ->inline(false),
                        Forms\Components\Toggle::make('is_pensionable')
                            ->label('Pensionable?')
                            ->helperText('Include in SSNIT/pension calculation')
                            ->inline(false),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)->inline(false),
                    ]),

                Section::make('Notes')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Textarea::make('description')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('code')
                    ->badge()->color('gray'),
                Tables\Columns\TextColumn::make('calculation_type')
                    ->label('Calc. Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fixed'      => 'primary',
                        'percentage' => 'warning',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('default_amount')
                    ->label('Default Amount')
                    ->money('GHS')
                    ->getStateUsing(fn (AllowanceType $r) => $r->calculation_type === 'fixed' ? $r->default_amount : null)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('percentage_value')
                    ->label('Pct.')
                    ->suffix('%')
                    ->getStateUsing(fn (AllowanceType $r) => $r->calculation_type === 'percentage' ? $r->percentage_value : null)
                    ->placeholder('—'),
                Tables\Columns\IconColumn::make('is_taxable')->label('Taxable')->boolean(),
                Tables\Columns\IconColumn::make('is_pensionable')->label('Pensionable')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('calculation_type')->options(AllowanceType::CALCULATION_TYPES),
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
                Tables\Filters\TernaryFilter::make('is_taxable')->label('Taxable'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAllowanceTypes::route('/'),
            'create' => Pages\CreateAllowanceType::route('/create'),
            'edit'   => Pages\EditAllowanceType::route('/{record}/edit'),
        ];
    }
}