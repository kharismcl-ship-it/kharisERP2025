<?php

namespace Modules\HR\Filament\Resources\Staff\MyEmployeeGoalResource\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;
use Modules\HR\Models\EmployeeGoal;

class EmployeeGoalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->weight('bold')
                    ->limit(50),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'high'   => 'danger',
                        'medium' => 'warning',
                        'low'    => 'gray',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => EmployeeGoal::PRIORITIES[$state] ?? ucfirst($state)),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'completed'   => 'success',
                        'in_progress' => 'warning',
                        'not_started' => 'gray',
                        'cancelled'   => 'danger',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => EmployeeGoal::STATUSES[$state] ?? ucfirst(str_replace('_', ' ', $state))),

                Tables\Columns\TextColumn::make('completion_percentage')
                    ->label('Progress')
                    ->suffix('%')
                    ->numeric(decimalPlaces: 0)
                    ->color(fn ($state) => match (true) {
                        $state >= 80 => 'success',
                        $state >= 40 => 'warning',
                        default      => 'danger',
                    }),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Target Date')
                    ->date()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('performanceCycle.name')
                    ->label('Cycle')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(EmployeeGoal::STATUSES),
                Tables\Filters\SelectFilter::make('priority')
                    ->options(EmployeeGoal::PRIORITIES),
            ]);
    }
}
