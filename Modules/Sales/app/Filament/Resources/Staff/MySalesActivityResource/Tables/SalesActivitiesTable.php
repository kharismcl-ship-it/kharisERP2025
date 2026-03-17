<?php

namespace Modules\Sales\Filament\Resources\Staff\MySalesActivityResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Modules\Sales\Models\SalesActivity;

class SalesActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('scheduled_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->weight('bold')
                    ->limit(50),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Scheduled')
                    ->dateTime('M d, Y H:i')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime('M d, Y H:i')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('outcome')
                    ->limit(40)
                    ->placeholder('—'),
                Tables\Columns\IconColumn::make('completed_at')
                    ->label('Done')
                    ->boolean()
                    ->getStateUsing(fn (SalesActivity $r) => $r->completed_at !== null),
            ])
            ->actions([ViewAction::make()])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(array_combine(
                        SalesActivity::TYPES,
                        array_map('ucfirst', SalesActivity::TYPES)
                    )),
                Tables\Filters\TernaryFilter::make('completed')
                    ->label('Completed')
                    ->queries(
                        true: fn (Builder $q) => $q->whereNotNull('completed_at'),
                        false: fn (Builder $q) => $q->whereNull('completed_at'),
                    ),
            ]);
    }
}
