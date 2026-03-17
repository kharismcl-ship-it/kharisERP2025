<?php

namespace Modules\HR\Filament\Resources\Staff\MyPerformanceReviewResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;

class PerformanceReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('performanceCycle.name')
                    ->label('Cycle')
                    ->weight('bold')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('reviewer.full_name')
                    ->label('Reviewed By')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->numeric(decimalPlaces: 1)
                    ->suffix(' / 5')
                    ->color(fn ($state) => match (true) {
                        $state >= 4   => 'success',
                        $state >= 2.5 => 'warning',
                        default       => 'danger',
                    })
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('comments')
                    ->label('Comments')
                    ->limit(60)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->date()
                    ->sortable(),
            ])
            ->actions([ViewAction::make()]);
    }
}
