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
use Modules\HR\Filament\Resources\BenefitTypeResource\Pages;
use Modules\HR\Filament\Resources\BenefitTypeResource\RelationManagers\EmployeeBenefitsRelationManager;
use Modules\HR\Models\BenefitType;

class BenefitTypeResource extends Resource
{
    protected static ?string $model = BenefitType::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedGift;

    protected static string|\UnitEnum|null $navigationGroup = 'Benefits & Loans';

    protected static ?int $navigationSort = 64;

    protected static ?string $navigationLabel = 'Benefit Types';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Benefit Details')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable()->preload()->required(),
                        Forms\Components\TextInput::make('name')->required()->maxLength(150),
                        Forms\Components\Select::make('category')
                            ->options(BenefitType::CATEGORIES)
                            ->required()->native(false),
                        Forms\Components\TextInput::make('provider')->maxLength(150)->nullable(),
                        Forms\Components\TextInput::make('employer_contribution')
                            ->label('Employer Contribution')
                            ->numeric()->prefix('GHS')->nullable(),
                        Forms\Components\Toggle::make('employee_contribution_required')
                            ->label('Employee Contribution Required')
                            ->live()->inline(false),
                        Forms\Components\TextInput::make('employee_contribution')
                            ->numeric()->prefix('GHS')
                            ->visible(fn ($get) => $get('employee_contribution_required'))
                            ->nullable(),
                        Forms\Components\Toggle::make('is_taxable')->inline(false),
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
                Tables\Columns\TextColumn::make('name')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'health'     => 'success',
                        'insurance'  => 'info',
                        'transport'  => 'warning',
                        'housing'    => 'primary',
                        'education'  => 'info',
                        'retirement' => 'gray',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => BenefitType::CATEGORIES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('provider')->placeholder('—'),
                Tables\Columns\TextColumn::make('employer_contribution')
                    ->label('Employer Contribution')
                    ->money('GHS')->placeholder('—'),
                Tables\Columns\IconColumn::make('employee_contribution_required')
                    ->label('Employee Pays?')->boolean(),
                Tables\Columns\IconColumn::make('is_taxable')->label('Taxable')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('category')->options(BenefitType::CATEGORIES),
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

    public static function getRelations(): array
    {
        return [
            EmployeeBenefitsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBenefitTypes::route('/'),
            'create' => Pages\CreateBenefitType::route('/create'),
            'view'   => Pages\ViewBenefitType::route('/{record}'),
            'edit'   => Pages\EditBenefitType::route('/{record}/edit'),
        ];
    }
}