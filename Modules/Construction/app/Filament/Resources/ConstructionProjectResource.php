<?php

namespace Modules\Construction\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
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
use Modules\Construction\Filament\Resources\ConstructionProjectResource\Pages;
use Modules\Construction\Filament\Resources\ConstructionProjectResource\RelationManagers;
use Modules\Construction\Models\ConstructionProject;

class ConstructionProjectResource extends Resource
{
    protected static ?string $model = ConstructionProject::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|\UnitEnum|null $navigationGroup = 'Construction';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Project Details')->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('location')->maxLength(255),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('client_name')->label('Client Name')->maxLength(255),
                    TextInput::make('client_contact')->label('Client Contact')->maxLength(255),
                ]),
                TextInput::make('project_manager')->label('Project Manager')->maxLength(255),
                Textarea::make('description')->rows(3)->columnSpanFull(),
            ]),

            Section::make('Timeline')->schema([
                Grid::make(3)->schema([
                    DatePicker::make('start_date'),
                    DatePicker::make('expected_end_date')->label('Expected End'),
                    DatePicker::make('actual_end_date')->label('Actual End'),
                ]),
            ]),

            Section::make('Financials')->schema([
                Grid::make(3)->schema([
                    TextInput::make('contract_value')->label('Contract Value')->numeric()->prefix('GHS')->step(0.01),
                    TextInput::make('budget')->numeric()->prefix('GHS')->step(0.01),
                    TextInput::make('total_spent')->label('Total Spent')->numeric()->prefix('GHS')->step(0.01)->disabled(),
                ]),
            ]),

            Section::make('Status')->schema([
                Grid::make(2)->schema([
                    Select::make('status')
                        ->options(array_combine(
                            ConstructionProject::STATUSES,
                            array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), ConstructionProject::STATUSES))
                        ))
                        ->default('planning')
                        ->required(),
                    Textarea::make('notes')->rows(2),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('client_name')->label('Client'),
                TextColumn::make('location'),
                TextColumn::make('start_date')->date(),
                TextColumn::make('expected_end_date')->label('Expected End')->date(),
                TextColumn::make('budget')->money('GHS'),
                TextColumn::make('total_spent')->label('Spent')->money('GHS'),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'    => 'success',
                        'planning'  => 'info',
                        'completed' => 'success',
                        'on_hold'   => 'warning',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(
                        ConstructionProject::STATUSES,
                        array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), ConstructionProject::STATUSES))
                    )),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProjectPhasesRelationManager::class,
            RelationManagers\ProjectTasksRelationManager::class,
            RelationManagers\MaterialUsagesRelationManager::class,
            RelationManagers\ProjectBudgetItemsRelationManager::class,
            RelationManagers\WorkersRelationManager::class,
            RelationManagers\SiteMonitorsRelationManager::class,
            RelationManagers\MonitoringReportsRelationManager::class,
            RelationManagers\ConstructionDocumentsRelationManager::class,
            RelationManagers\ContractorRequestsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListConstructionProjects::route('/'),
            'create' => Pages\CreateConstructionProject::route('/create'),
            'view'   => Pages\ViewConstructionProject::route('/{record}'),
            'edit'   => Pages\EditConstructionProject::route('/{record}/edit'),
        ];
    }
}
