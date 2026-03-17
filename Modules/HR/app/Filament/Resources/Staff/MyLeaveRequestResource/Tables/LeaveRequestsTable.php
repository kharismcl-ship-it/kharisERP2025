<?php

namespace Modules\HR\Filament\Resources\Staff\MyLeaveRequestResource\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;
use Modules\HR\Models\LeaveRequest;

class LeaveRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('start_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('leaveType.name')
                    ->label('Type')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('end_date')->date(),
                Tables\Columns\TextColumn::make('days_requested')
                    ->label('Days')
                    ->getStateUsing(fn (LeaveRequest $r) => $r->start_date->diffInDays($r->end_date) + 1),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'          => 'warning',
                        'pending_approval' => 'info',
                        'approved'         => 'success',
                        'rejected'         => 'danger',
                        'cancelled'        => 'gray',
                        default            => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending_approval' => 'In Review',
                        default            => ucfirst(str_replace('_', ' ', $state)),
                    }),
                Tables\Columns\TextColumn::make('created_at')->date()->label('Submitted'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (LeaveRequest $r) => $r->status === 'pending'),
                DeleteAction::make()
                    ->visible(fn (LeaveRequest $r) => $r->status === 'pending'),
            ]);
    }
}
