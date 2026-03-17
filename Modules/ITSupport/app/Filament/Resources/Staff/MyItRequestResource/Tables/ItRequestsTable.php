<?php

namespace Modules\ITSupport\Filament\Resources\Staff\MyItRequestResource\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;
use Modules\ITSupport\Models\ItRequest;

class ItRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Ref')
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('subject')->wrap()->limit(50),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ItRequest::CATEGORIES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'critical' => 'danger',
                        'high'     => 'warning',
                        'medium'   => 'info',
                        default    => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'resolved'     => 'success',
                        'closed'       => 'success',
                        'in_progress'  => 'warning',
                        'pending_info' => 'gray',
                        'open'         => 'info',
                        'cancelled'    => 'danger',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ItRequest::STATUSES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('assignedToEmployee.full_name')
                    ->label('Assigned To')
                    ->placeholder('Unassigned'),
                Tables\Columns\TextColumn::make('created_at')->date()->label('Submitted'),
            ])
            ->actions([
                ViewAction::make(),
                DeleteAction::make()
                    ->visible(fn (ItRequest $r) => $r->status === 'open'),
            ]);
    }
}
