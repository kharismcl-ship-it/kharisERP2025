<?php

namespace Modules\Construction\Filament\Resources\ContractorResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Construction\Models\ProjectTask;

class ProjectTasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $title = 'Assigned Tasks';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255)->columnSpanFull(),
            Select::make('construction_project_id')
                ->relationship('project', 'name')
                ->required()
                ->searchable()
                ->preload(),
            Select::make('project_phase_id')
                ->relationship('phase', 'name')
                ->searchable()
                ->preload()
                ->placeholder('No phase'),
            Select::make('status')
                ->options(array_combine(
                    ProjectTask::STATUSES,
                    array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), ProjectTask::STATUSES))
                ))
                ->default('pending')
                ->required(),
            Select::make('priority')
                ->options(ProjectTask::PRIORITIES)
                ->default(2)
                ->required(),
            DatePicker::make('due_date'),
            Textarea::make('description')->rows(2)->columnSpanFull(),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->limit(40),
                TextColumn::make('project.name')->label('Project')->searchable(),
                TextColumn::make('phase.name')->label('Phase')->placeholder('—'),
                TextColumn::make('priority')
                    ->formatStateUsing(fn ($state) => ProjectTask::PRIORITIES[$state] ?? '—'),
                TextColumn::make('due_date')->date()->label('Due')->sortable(),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed'   => 'success',
                        'in_progress' => 'warning',
                        'blocked'     => 'danger',
                        default       => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(
                        ProjectTask::STATUSES,
                        array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), ProjectTask::STATUSES))
                    )),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->defaultSort('due_date');
    }
}
