<?php

namespace Modules\Fleet\Filament\Resources\Staff\MyTripLogResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;
use Modules\Fleet\Models\TripLog;

class TripLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('trip_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('trip_reference')
                    ->label('Ref')
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('trip_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vehicle.registration_number')
                    ->label('Vehicle')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('origin')
                    ->limit(30),
                Tables\Columns\TextColumn::make('destination')
                    ->limit(30),
                Tables\Columns\TextColumn::make('purpose')
                    ->limit(40)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('distance_km')
                    ->label('Distance (km)')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed'   => 'success',
                        'in_progress' => 'warning',
                        'planned'     => 'info',
                        'cancelled'   => 'danger',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => ucfirst(str_replace('_', ' ', $state))),
            ])
            ->actions([ViewAction::make()])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(
                        TripLog::STATUSES,
                        array_map(fn ($s) => ucfirst(str_replace('_', ' ', $s)), TripLog::STATUSES)
                    )),
            ]);
    }
}
