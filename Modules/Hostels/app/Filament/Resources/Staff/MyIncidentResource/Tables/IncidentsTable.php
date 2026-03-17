<?php

namespace Modules\Hostels\Filament\Resources\Staff\MyIncidentResource\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Hostels\Models\Incident;

class IncidentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('hostel.name')
                    ->label('Hostel')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('severity')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'critical' => 'danger',
                        'major'    => 'warning',
                        'minor'    => 'info',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'open'      => 'warning',
                        'escalated' => 'danger',
                        'resolved'  => 'success',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('reported_at')
                    ->label('Reported')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('reported_at', 'desc')
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (Incident $record) => $record->status === 'open'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('severity')
                    ->options([
                        'minor'    => 'Minor',
                        'major'    => 'Major',
                        'critical' => 'Critical',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open'      => 'Open',
                        'escalated' => 'Escalated',
                        'resolved'  => 'Resolved',
                    ]),
            ]);
    }
}
