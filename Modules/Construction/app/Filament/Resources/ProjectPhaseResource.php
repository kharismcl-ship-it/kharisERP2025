<?php

namespace Modules\Construction\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
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
use Modules\Construction\Filament\Resources\ProjectPhaseResource\Pages;
use Modules\Construction\Models\ConstructionProject;
use Modules\Construction\Models\ProjectPhase;

class ProjectPhaseResource extends Resource
{
    protected static ?string $model = ProjectPhase::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Construction';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Project Phases';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Phase Details')->schema([
                Grid::make(2)->schema([
                    Select::make('construction_project_id')
                        ->label('Project')
                        ->relationship('project', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('name')->required()->maxLength(255),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('order')
                        ->numeric()
                        ->default(1)
                        ->required(),
                    Select::make('status')
                        ->options(array_combine(
                            ProjectPhase::STATUSES,
                            array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), ProjectPhase::STATUSES))
                        ))
                        ->default('pending')
                        ->required(),
                ]),
                Textarea::make('description')->rows(3)->columnSpanFull(),
            ]),

            Section::make('Timeline')->schema([
                Grid::make(2)->schema([
                    DatePicker::make('planned_start')->label('Planned Start'),
                    DatePicker::make('planned_end')->label('Planned End'),
                ]),
                Grid::make(2)->schema([
                    DatePicker::make('actual_start')->label('Actual Start'),
                    DatePicker::make('actual_end')->label('Actual End'),
                ]),
            ]),

            Section::make('Budget & Progress')->schema([
                Grid::make(2)->schema([
                    TextInput::make('budget')
                        ->numeric()
                        ->prefix('GHS')
                        ->step(0.01),
                    TextInput::make('progress_percent')
                        ->label('Progress (%)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->suffix('%'),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('spent')
                        ->numeric()
                        ->prefix('GHS')
                        ->step(0.01)
                        ->readOnly(),
                ]),
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
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed'   => 'success',
                        'in_progress' => 'warning',
                        'pending'     => 'gray',
                        'on_hold'     => 'danger',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                TextColumn::make('progress_percent')
                    ->label('Progress')
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('planned_start')
                    ->label('Planned Start')
                    ->date()
                    ->sortable(),
                TextColumn::make('planned_end')
                    ->label('Planned End')
                    ->date()
                    ->sortable(),
                TextColumn::make('budget')
                    ->money('GHS')
                    ->sortable(),
                TextColumn::make('spent')
                    ->money('GHS')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(
                        ProjectPhase::STATUSES,
                        array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), ProjectPhase::STATUSES))
                    )),
                Tables\Filters\SelectFilter::make('construction_project_id')
                    ->label('Project')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('start')
                        ->label('Start')
                        ->icon('heroicon-o-play')
                        ->color('success')
                        ->visible(fn (ProjectPhase $record) => $record->status === 'pending')
                        ->action(fn (ProjectPhase $record) => $record->update(['status' => 'in_progress']))
                        ->requiresConfirmation(),
                    Action::make('complete')
                        ->label('Complete')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (ProjectPhase $record) => $record->status === 'in_progress')
                        ->action(fn (ProjectPhase $record) => $record->update(['status' => 'completed', 'progress_percent' => 100]))
                        ->requiresConfirmation(),
                    Action::make('hold')
                        ->label('Hold')
                        ->icon('heroicon-o-pause-circle')
                        ->color('warning')
                        ->visible(fn (ProjectPhase $record) => $record->status === 'in_progress')
                        ->action(fn (ProjectPhase $record) => $record->update(['status' => 'on_hold']))
                        ->requiresConfirmation(),
                    Action::make('reopen')
                        ->label('Reopen')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->visible(fn (ProjectPhase $record) => $record->status === 'on_hold')
                        ->action(fn (ProjectPhase $record) => $record->update(['status' => 'in_progress']))
                        ->requiresConfirmation(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProjectPhases::route('/'),
            'create' => Pages\CreateProjectPhase::route('/create'),
            'view'   => Pages\ViewProjectPhase::route('/{record}'),
            'edit'   => Pages\EditProjectPhase::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
