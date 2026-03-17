<?php

namespace Modules\HR\Filament\Resources\Staff\MyGrievanceResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;
use Modules\HR\Models\GrievanceCase;

class GrievancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('grievance_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state)))
                    ->searchable(),
                Tables\Columns\TextColumn::make('filed_date')
                    ->date()->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'resolved', 'closed'        => 'success',
                        'under_investigation',
                        'hearing_scheduled'          => 'warning',
                        'escalated'                  => 'danger',
                        default                      => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => GrievanceCase::STATUSES[$state] ?? ucfirst(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('resolution')
                    ->label('Resolution')
                    ->placeholder('Pending review')
                    ->limit(60),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }
}
