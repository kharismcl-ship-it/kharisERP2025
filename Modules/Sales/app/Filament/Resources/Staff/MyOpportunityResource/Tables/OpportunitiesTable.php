<?php

namespace Modules\Sales\Filament\Resources\Staff\MyOpportunityResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;
use Modules\Sales\Models\SalesOpportunity;

class OpportunitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('expected_close_date', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->weight('bold')
                    ->limit(50),
                Tables\Columns\TextColumn::make('contact.full_name')
                    ->label('Contact')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('stage')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'closed_won'  => 'success',
                        'closed_lost' => 'danger',
                        'negotiation' => 'warning',
                        default       => 'info',
                    })
                    ->formatStateUsing(fn (string $state) => ucfirst(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('estimated_value')
                    ->label('Value')
                    ->money('KES'),
                Tables\Columns\TextColumn::make('probability_pct')
                    ->label('Probability')
                    ->suffix('%'),
                Tables\Columns\TextColumn::make('expected_close_date')
                    ->label('Close Date')
                    ->date()
                    ->sortable(),
            ])
            ->actions([ViewAction::make()])
            ->filters([
                Tables\Filters\SelectFilter::make('stage')
                    ->options(array_combine(
                        SalesOpportunity::STAGES,
                        array_map(fn ($s) => ucfirst(str_replace('_', ' ', $s)), SalesOpportunity::STAGES)
                    )),
            ]);
    }
}
