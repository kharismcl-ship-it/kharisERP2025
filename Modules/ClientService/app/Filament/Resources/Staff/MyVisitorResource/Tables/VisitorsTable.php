<?php

namespace Modules\ClientService\Filament\Resources\Staff\MyVisitorResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class VisitorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('check_in_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('organization')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('purpose_of_visit')
                    ->label('Purpose')
                    ->limit(40)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('check_in_at')
                    ->label('Check In')
                    ->dateTime('M d, Y H:i'),
                Tables\Columns\TextColumn::make('check_out_at')
                    ->label('Check Out')
                    ->dateTime('M d, Y H:i')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Duration')
                    ->placeholder('—'),
                Tables\Columns\IconColumn::make('is_checked_out')
                    ->label('Left')
                    ->boolean(),
            ])
            ->actions([ViewAction::make()])
            ->filters([
                Tables\Filters\TernaryFilter::make('checked_out')
                    ->label('Checked Out')
                    ->queries(
                        true: fn (Builder $q) => $q->whereNotNull('check_out_at'),
                        false: fn (Builder $q) => $q->whereNull('check_out_at'),
                    ),
            ]);
    }
}
