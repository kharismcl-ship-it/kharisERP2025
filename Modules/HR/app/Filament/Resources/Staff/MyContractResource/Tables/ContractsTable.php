<?php

namespace Modules\HR\Filament\Resources\Staff\MyContractResource\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;
use Modules\HR\Models\EmploymentContract;

class ContractsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('start_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('contract_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state)))
                    ->color('info'),
                Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->placeholder('Ongoing'),
                Tables\Columns\TextColumn::make('probation_end_date')
                    ->date()
                    ->label('Probation End')
                    ->placeholder('—'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->getStateUsing(fn (EmploymentContract $r) => ! $r->end_date || $r->end_date->isFuture())
                    ->boolean(),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }
}
