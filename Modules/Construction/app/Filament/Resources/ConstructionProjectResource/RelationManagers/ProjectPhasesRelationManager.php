<?php

namespace Modules\Construction\Filament\Resources\ConstructionProjectResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Modules\Construction\Models\ProjectPhase;

class ProjectPhasesRelationManager extends RelationManager
{
    protected static string $relationship = 'phases';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('order')->numeric()->default(0),
            Textarea::make('description')->rows(2)->columnSpanFull(),
            DatePicker::make('planned_start')->label('Planned Start'),
            DatePicker::make('planned_end')->label('Planned End'),
            DatePicker::make('actual_start')->label('Actual Start'),
            DatePicker::make('actual_end')->label('Actual End'),
            TextInput::make('budget')->numeric()->prefix('GHS')->step(0.01),
            TextInput::make('progress_percent')->label('Progress (%)')->numeric()->minValue(0)->maxValue(100)->default(0),
            Select::make('status')
                ->options(array_combine(ProjectPhase::STATUSES, array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), ProjectPhase::STATUSES))))
                ->default('pending'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')->label('#')->sortable(),
                TextColumn::make('name'),
                TextColumn::make('planned_start')->date()->label('Planned Start'),
                TextColumn::make('planned_end')->date()->label('Planned End'),
                TextColumn::make('progress_percent')->label('Progress')->suffix('%'),
                TextColumn::make('budget')->money('GHS'),
                TextColumn::make('spent')->money('GHS'),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed'   => 'success',
                        'in_progress' => 'warning',
                        'pending'     => 'gray',
                        'on_hold'     => 'danger',
                        default       => 'gray',
                    }),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->defaultSort('order');
    }
}
