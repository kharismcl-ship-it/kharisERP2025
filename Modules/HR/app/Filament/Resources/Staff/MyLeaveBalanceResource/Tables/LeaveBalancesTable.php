<?php

namespace Modules\HR\Filament\Resources\Staff\MyLeaveBalanceResource\Tables;

use Filament\Tables\Table;
use Filament\Tables;

class LeaveBalancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('leaveType.name')
                    ->label('Leave Type')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('year')
                    ->label('Year'),

                Tables\Columns\TextColumn::make('initial_balance')
                    ->label('Allocated')
                    ->suffix(' days')
                    ->numeric(decimalPlaces: 1),

                Tables\Columns\TextColumn::make('carried_over')
                    ->label('Carried Over')
                    ->suffix(' days')
                    ->numeric(decimalPlaces: 1)
                    ->placeholder('0'),

                Tables\Columns\TextColumn::make('used_balance')
                    ->label('Used')
                    ->suffix(' days')
                    ->numeric(decimalPlaces: 1)
                    ->color('warning'),

                Tables\Columns\TextColumn::make('current_balance')
                    ->label('Available')
                    ->suffix(' days')
                    ->numeric(decimalPlaces: 1)
                    ->weight('bold')
                    ->color(fn ($state) => (float) $state > 0 ? 'success' : 'danger'),
            ])
            ->paginated(false);
    }
}
