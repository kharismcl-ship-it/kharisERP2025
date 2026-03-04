<?php

namespace Modules\HR\Filament\Resources;

use Modules\HR\Filament\Clusters\HrSetupCluster;

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
use Modules\HR\Filament\Resources\DeductionTypeResource\Pages;
use Modules\HR\Models\DeductionType;

class DeductionTypeResource extends Resource
{
    protected static ?string $cluster = HrSetupCluster::class;
    protected static ?string $model = DeductionType::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedMinusCircle;


    protected static ?int $navigationSort = 52;

    protected static ?string $navigationLabel = 'Deduction Types';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Deduction Details')
                    ->description('Configure statutory or voluntary payroll deductions')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable()->preload(),
                        Forms\Components\TextInput::make('name')->maxLength(100),
                        Forms\Components\TextInput::make('code')
                            ->maxLength(30)
                            ->unique(ignoreRecord: true)
                            ->helperText('e.g. PAYE, SSNIT, LOAN1'),
                        Forms\Components\Select::make('category')
                            ->options(DeductionType::CATEGORIES)
                            ->required()->native(false),
                        Forms\Components\Select::make('calculation_type')
                            ->options(DeductionType::CALCULATION_TYPES)
                            ->required()->native(false)->live(),
                        Forms\Components\TextInput::make('default_amount')
                            ->numeric()->prefix('GHS')
                            ->visible(fn ($get) => $get('calculation_type') === 'fixed'),
                        Forms\Components\TextInput::make('percentage_value')
                            ->numeric()->suffix('%')
                            ->visible(fn ($get) => $get('calculation_type') === 'percentage'),
                        Forms\Components\TextInput::make('gl_account_code')
                            ->label('GL Account Code / Liability Account')
                            ->maxLength(30),
                    ]),

                Section::make('Status')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Toggle::make('is_active')->default(true)->inline(false),
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
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'tax'             => 'danger',
                        'social_security' => 'warning',
                        'pension'         => 'info',
                        'loan'            => 'primary',
                        default           => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => DeductionType::CATEGORIES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('calculation_type')
                    ->label('Calc.')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fixed'      => 'primary',
                        'percentage' => 'warning',
                        default      => 'gray',
                    }),
                Tables\Columns\TextColumn::make('default_amount')
                    ->money('GHS')
                    ->getStateUsing(fn (DeductionType $r) => $r->calculation_type === 'fixed' ? $r->default_amount : null)
                    ->placeholder('—'),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('category')->options(DeductionType::CATEGORIES),
                Tables\Filters\SelectFilter::make('calculation_type')->options(DeductionType::CALCULATION_TYPES),
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
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
            'index'  => Pages\ListDeductionTypes::route('/'),
            'create' => Pages\CreateDeductionType::route('/create'),
            'view'   => Pages\ViewDeductionType::route('/{record}'),
            'edit'   => Pages\EditDeductionType::route('/{record}/edit'),
        ];
    }
}