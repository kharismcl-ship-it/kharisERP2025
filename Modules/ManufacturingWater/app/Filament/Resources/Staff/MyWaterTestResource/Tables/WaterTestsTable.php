<?php

namespace Modules\ManufacturingWater\Filament\Resources\Staff\MyWaterTestResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;
use Modules\ManufacturingWater\Models\MwWaterTestRecord;

class WaterTestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('test_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('test_date')
                    ->date()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('plant.name')
                    ->label('Plant')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('test_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('ph')
                    ->label('pH')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('turbidity_ntu')
                    ->label('Turbidity (NTU)')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('chlorine_residual')
                    ->label('Chlorine')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('tested_by')
                    ->label('Tested By')
                    ->placeholder('—'),
                Tables\Columns\IconColumn::make('passed')
                    ->label('Pass')
                    ->boolean(),
            ])
            ->actions([ViewAction::make()])
            ->filters([
                Tables\Filters\SelectFilter::make('test_type')
                    ->options(array_combine(
                        MwWaterTestRecord::TEST_TYPES,
                        array_map('ucfirst', MwWaterTestRecord::TEST_TYPES)
                    )),
                Tables\Filters\TernaryFilter::make('passed')->label('Passed'),
            ]);
    }
}
