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
use Modules\Construction\Filament\Resources\ProjectTaskResource\Pages;
use Modules\Construction\Models\ProjectTask;

class ProjectTaskResource extends Resource
{
    protected static ?string $model = ProjectTask::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-check-circle';

    protected static string|\UnitEnum|null $navigationGroup = 'Construction';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Project Tasks';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Task Details')->schema([
                Grid::make(2)->schema([
                    Select::make('construction_project_id')
                        ->label('Project')
                        ->relationship('project', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('project_phase_id')
                        ->label('Phase')
                        ->relationship('phase', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ]),
                Grid::make(2)->schema([
                    Select::make('contractor_id')
                        ->label('Contractor')
                        ->relationship('contractor', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ]),
                TextInput::make('name')->required()->maxLength(255)->columnSpanFull(),
                Grid::make(2)->schema([
                    Select::make('priority')
                        ->options(ProjectTask::PRIORITIES)
                        ->default(2)
                        ->required(),
                    Select::make('status')
                        ->options(array_combine(
                            ProjectTask::STATUSES,
                            array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), ProjectTask::STATUSES))
                        ))
                        ->default('pending')
                        ->required(),
                ]),
            ]),

            Section::make('Dates & Notes')->schema([
                Grid::make(2)->schema([
                    DatePicker::make('due_date')->label('Due Date'),
                    DatePicker::make('completed_at')
                        ->label('Completed At')
                        ->readOnly(),
                ]),
                Textarea::make('description')->rows(3)->columnSpanFull(),
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
                TextColumn::make('phase.name')
                    ->label('Phase')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contractor.name')
                    ->label('Contractor')
                    ->placeholder('Unassigned'),
                TextColumn::make('priority')
                    ->badge()
                    ->color(fn ($state): string => match ((int) $state) {
                        1 => 'gray',
                        2 => 'warning',
                        3 => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ProjectTask::PRIORITIES[(int) $state] ?? $state),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed'   => 'success',
                        'in_progress' => 'warning',
                        'blocked'     => 'danger',
                        'pending'     => 'gray',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->label('Completed')
                    ->date()
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(
                        ProjectTask::STATUSES,
                        array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), ProjectTask::STATUSES))
                    )),
                Tables\Filters\SelectFilter::make('priority')
                    ->options(ProjectTask::PRIORITIES),
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
                        ->visible(fn (ProjectTask $record) => $record->status === 'pending')
                        ->action(fn (ProjectTask $record) => $record->update(['status' => 'in_progress']))
                        ->requiresConfirmation(),
                    Action::make('complete')
                        ->label('Complete')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (ProjectTask $record) => $record->status === 'in_progress')
                        ->action(fn (ProjectTask $record) => $record->update([
                            'status'       => 'completed',
                            'completed_at' => now(),
                        ]))
                        ->requiresConfirmation(),
                    Action::make('block')
                        ->label('Block')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->visible(fn (ProjectTask $record) => in_array($record->status, ['pending', 'in_progress']))
                        ->action(fn (ProjectTask $record) => $record->update(['status' => 'blocked']))
                        ->requiresConfirmation(),
                    Action::make('reopen')
                        ->label('Reopen')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->visible(fn (ProjectTask $record) => in_array($record->status, ['blocked', 'completed']))
                        ->action(fn (ProjectTask $record) => $record->update(['status' => 'pending']))
                        ->requiresConfirmation(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('due_date');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProjectTasks::route('/'),
            'create' => Pages\CreateProjectTask::route('/create'),
            'view'   => Pages\ViewProjectTask::route('/{record}'),
            'edit'   => Pages\EditProjectTask::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
