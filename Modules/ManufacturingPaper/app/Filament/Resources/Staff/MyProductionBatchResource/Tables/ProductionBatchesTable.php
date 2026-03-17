<?php

namespace Modules\ManufacturingPaper\Filament\Resources\Staff\MyProductionBatchResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;
use Modules\ManufacturingPaper\Models\MpProductionBatch;

class ProductionBatchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('start_time', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('batch_number')
                    ->label('Batch #')
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('plant.name')
                    ->label('Plant')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('paperGrade.name')
                    ->label('Grade')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('quantity_planned')
                    ->label('Planned')
                    ->suffix(fn (MpProductionBatch $r) => ' ' . $r->unit),
                Tables\Columns\TextColumn::make('quantity_produced')
                    ->label('Produced')
                    ->suffix(fn (MpProductionBatch $r) => ' ' . $r->unit)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('efficiency_percent')
                    ->label('Efficiency')
                    ->suffix('%')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed'   => 'success',
                        'in_progress' => 'warning',
                        'planned'     => 'info',
                        'on_hold'     => 'gray',
                        'cancelled'   => 'danger',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => ucfirst(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Started')
                    ->dateTime('M d, H:i')
                    ->placeholder('—'),
            ])
            ->actions([ViewAction::make()])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(
                        MpProductionBatch::STATUSES,
                        array_map(fn ($s) => ucfirst(str_replace('_', ' ', $s)), MpProductionBatch::STATUSES)
                    )),
            ]);
    }
}
