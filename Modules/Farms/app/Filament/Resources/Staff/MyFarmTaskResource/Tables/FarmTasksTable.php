<?php

namespace Modules\Farms\Filament\Resources\Staff\MyFarmTaskResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;
use Modules\Farms\Models\FarmTask;

class FarmTasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->weight('bold')
                    ->limit(50)
                    ->description(fn (FarmTask $r) => $r->farm?->name),

                Tables\Columns\TextColumn::make('task_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state))),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'urgent' => 'danger',
                        'high'   => 'warning',
                        'medium' => 'info',
                        'low'    => 'gray',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due')
                    ->date()
                    ->color(fn (FarmTask $r) => ! $r->completed_at && $r->due_date && now()->gt($r->due_date) ? 'danger' : null)
                    ->sortable(),

                Tables\Columns\IconColumn::make('completed_at')
                    ->label('Done')
                    ->boolean()
                    ->getStateUsing(fn (FarmTask $r) => $r->completed_at !== null),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('complete')
                    ->label('Mark Done')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Mark Task as Complete?')
                    ->action(fn (FarmTask $record) => $record->update(['completed_at' => now()]))
                    ->visible(fn (FarmTask $record) => $record->completed_at === null),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('task_type')
                    ->label('Type')
                    ->options(array_combine(
                        FarmTask::TASK_TYPES,
                        array_map(fn ($t) => ucfirst(str_replace('_', ' ', $t)), FarmTask::TASK_TYPES)
                    )),
                Tables\Filters\SelectFilter::make('priority')
                    ->options(array_combine(
                        FarmTask::PRIORITIES,
                        array_map('ucfirst', FarmTask::PRIORITIES)
                    )),
                Tables\Filters\TernaryFilter::make('completed')
                    ->label('Completed')
                    ->queries(
                        true:  fn ($q) => $q->whereNotNull('completed_at'),
                        false: fn ($q) => $q->whereNull('completed_at'),
                    ),
            ]);
    }
}
