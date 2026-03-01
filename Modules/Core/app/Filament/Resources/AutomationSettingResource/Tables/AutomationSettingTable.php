<?php

namespace Modules\Core\Filament\Resources\AutomationSettingResource\Tables;

use Filament\Tables;
use Filament\Tables\Table;

class AutomationSettingTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('module')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('action')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('company_id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_enabled')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('schedule_type')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('schedule_value')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_run_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('next_run_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('config')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Add your table filters here
            ])
            ->actions([
                // Add your table actions here
            ])
            ->bulkActions([
                // Add your bulk actions here
            ]);
    }
}
