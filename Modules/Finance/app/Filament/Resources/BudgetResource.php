<?php

namespace Modules\Finance\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
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
use Modules\Finance\Filament\Resources\BudgetResource\Pages;
use Modules\Finance\Filament\Resources\BudgetResource\RelationManagers\BudgetLinesRelationManager;
use Modules\Finance\Models\Budget;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartPie;

    protected static string|\UnitEnum|null $navigationGroup = 'General Ledger';

    protected static ?int $navigationSort = 41;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Budget Info')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('budget_year')
                            ->numeric()
                            ->required()
                            ->default(now()->year)
                            ->label('Budget Year'),
                        Forms\Components\Select::make('period_type')
                            ->options([
                                'annual'    => 'Annual',
                                'quarterly' => 'Quarterly',
                                'monthly'   => 'Monthly',
                            ])
                            ->default('annual')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft'    => 'Draft',
                                'approved' => 'Approved',
                                'active'   => 'Active',
                                'closed'   => 'Closed',
                            ])
                            ->default('draft')
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull()
                            ->rows(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('budget_year')->sortable(),
                Tables\Columns\TextColumn::make('period_type')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'draft'    => 'gray',
                        'approved' => 'info',
                        'active'   => 'success',
                        'closed'   => 'danger',
                        default    => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_budget')
                    ->money('GHS')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (Budget $record) => $record->status === 'draft')
                    ->action(fn (Budget $record) => $record->update([
                        'status'              => 'approved',
                        'approved_by_user_id' => auth()->id(),
                        'approved_at'         => now(),
                    ])),
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
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
            BudgetLinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'view'   => Pages\ViewBudget::route('/{record}'),
            'edit'   => Pages\EditBudget::route('/{record}/edit'),
        ];
    }
}