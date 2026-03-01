<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Farms\Filament\Resources\FarmBudgetResource\Pages;
use Modules\Farms\Models\FarmBudget;

class FarmBudgetResource extends Resource
{
    protected static ?string $model = FarmBudget::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Farm Budgets';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Budget Details')
                ->columns(3)
                ->schema([
                    TextInput::make('budget_name')->required()->maxLength(255)->columnSpanFull(),

                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('crop_cycle_id')
                        ->label('Crop Cycle (optional)')
                        ->relationship('cropCycle', 'crop_name')
                        ->searchable()
                        ->nullable(),

                    Select::make('category')
                        ->options(array_combine(
                            FarmBudget::CATEGORIES,
                            array_map('ucfirst', FarmBudget::CATEGORIES)
                        ))
                        ->default('general'),

                    TextInput::make('budget_year')
                        ->label('Year')
                        ->numeric()
                        ->default(now()->year)
                        ->required(),

                    Select::make('budget_month')
                        ->label('Month (optional)')
                        ->options([
                            1 => 'January',  2 => 'February', 3 => 'March',
                            4 => 'April',    5 => 'May',      6 => 'June',
                            7 => 'July',     8 => 'August',   9 => 'September',
                            10 => 'October', 11 => 'November', 12 => 'December',
                        ])
                        ->nullable(),
                ]),

            Section::make('Budget vs Actual')
                ->columns(2)
                ->schema([
                    TextInput::make('budgeted_amount')->label('Budgeted (GHS)')->required()->numeric()->prefix('GHS')->step(0.01),
                    TextInput::make('actual_amount')->label('Actual (GHS)')->numeric()->prefix('GHS')->step(0.01)->default(0),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([Textarea::make('notes')->rows(2)->columnSpanFull()]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('budget_name')->searchable()->limit(30),
                TextColumn::make('farm.name')->label('Farm')->sortable(),

                TextColumn::make('category')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('budget_year')->label('Year'),
                TextColumn::make('budget_month')
                    ->label('Month')
                    ->formatStateUsing(fn ($state) => $state
                        ? \Carbon\Carbon::create()->month($state)->format('M')
                        : 'Full Year'
                    ),

                TextColumn::make('budgeted_amount')->money('GHS')->label('Budget'),
                TextColumn::make('actual_amount')->money('GHS')->label('Actual'),

                TextColumn::make('variance')
                    ->label('Variance')
                    ->getStateUsing(fn ($record) => 'GHS ' . number_format($record->variance, 2))
                    ->color(fn ($record) => $record->variance > 0 ? 'danger' : 'success'),

                TextColumn::make('variance_pct')
                    ->label('% Var')
                    ->getStateUsing(fn ($record) =>
                        $record->variance_pct !== null ? $record->variance_pct . '%' : '—'
                    )
                    ->color(fn ($record) => ($record->variance_pct ?? 0) > 0 ? 'danger' : 'success'),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('category')
                    ->options(array_combine(FarmBudget::CATEGORIES, array_map('ucfirst', FarmBudget::CATEGORIES))),
            ])
            ->actions([ViewAction::make(), EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('budget_year', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmBudgets::route('/'),
            'create' => Pages\CreateFarmBudget::route('/create'),
            'view'   => Pages\ViewFarmBudget::route('/{record}'),
            'edit'   => Pages\EditFarmBudget::route('/{record}/edit'),
        ];
    }
}