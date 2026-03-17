<?php

namespace Modules\HR\Filament\Resources\Staff\MyTrainingResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;
use Modules\HR\Models\TrainingNomination;

class TrainingTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('trainingProgram.title')
                    ->label('Program')->weight('bold'),
                Tables\Columns\TextColumn::make('trainingProgram.start_date')
                    ->label('Start Date')->date()->placeholder('TBD'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'completed'  => 'success',
                        'attended'   => 'info',
                        'confirmed'  => 'primary',
                        'nominated'  => 'warning',
                        'cancelled'  => 'danger',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => TrainingNomination::STATUSES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('score')
                    ->suffix('/100')->placeholder('—'),
                Tables\Columns\TextColumn::make('completion_date')->date()->placeholder('—'),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }
}
