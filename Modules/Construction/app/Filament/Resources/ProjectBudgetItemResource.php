<?php

namespace Modules\Construction\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Construction\Filament\Resources\ProjectBudgetItemResource\Pages;
use Modules\Construction\Models\ProjectBudgetItem;

class ProjectBudgetItemResource extends Resource
{
    protected static ?string $model = ProjectBudgetItem::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Construction';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Budget Items';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Budget Item')->schema([
                Grid::make(2)->schema([
                    Select::make('construction_project_id')
                        ->label('Project')
                        ->relationship('project', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('category')
                        ->options(array_combine(
                            ProjectBudgetItem::CATEGORIES,
                            array_map('ucfirst', ProjectBudgetItem::CATEGORIES)
                        ))
                        ->required(),
                ]),
                Textarea::make('description')->rows(2)->columnSpanFull(),
                Grid::make(2)->schema([
                    TextInput::make('budgeted_amount')
                        ->label('Budgeted Amount')
                        ->numeric()
                        ->prefix('GHS')
                        ->step(0.01)
                        ->required(),
                    TextInput::make('actual_amount')
                        ->label('Actual Amount')
                        ->numeric()
                        ->prefix('GHS')
                        ->step(0.01)
                        ->default(0),
                ]),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(50),
                TextColumn::make('budgeted_amount')
                    ->label('Budgeted')
                    ->money('GHS')
                    ->sortable(),
                TextColumn::make('actual_amount')
                    ->label('Actual')
                    ->money('GHS')
                    ->sortable(),
                TextColumn::make('variance')
                    ->label('Variance')
                    ->getStateUsing(fn (ProjectBudgetItem $record) => $record->variance)
                    ->formatStateUsing(fn ($state) => 'GHS ' . number_format((float) $state, 2))
                    ->color(fn (ProjectBudgetItem $record): string => match (true) {
                        $record->variance > 0 => 'success',
                        $record->variance < 0 => 'danger',
                        default               => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('construction_project_id')
                    ->label('Project')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('category')
                    ->options(array_combine(
                        ProjectBudgetItem::CATEGORIES,
                        array_map('ucfirst', ProjectBudgetItem::CATEGORIES)
                    )),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('category');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProjectBudgetItems::route('/'),
            'create' => Pages\CreateProjectBudgetItem::route('/create'),
            'view'   => Pages\ViewProjectBudgetItem::route('/{record}'),
            'edit'   => Pages\EditProjectBudgetItem::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['description'];
    }
}
