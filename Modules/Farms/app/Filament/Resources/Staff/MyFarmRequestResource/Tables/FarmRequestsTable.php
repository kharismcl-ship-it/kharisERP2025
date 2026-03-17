<?php

namespace Modules\Farms\Filament\Resources\Staff\MyFarmRequestResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Farms\Models\FarmRequest;

class FarmRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('request_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('urgency')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'urgent' => 'danger',
                        'high'   => 'warning',
                        'medium' => 'info',
                        'low'    => 'gray',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'draft'     => 'gray',
                        'submitted' => 'warning',
                        'approved'  => 'success',
                        'rejected'  => 'danger',
                        'fulfilled' => 'primary',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (FarmRequest $record) => $record->status === 'draft'),
                Action::make('submit')
                    ->label('Submit')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Submit Request?')
                    ->modalDescription('Once submitted, you will no longer be able to edit this request.')
                    ->action(fn (FarmRequest $record) => $record->update(['status' => 'submitted']))
                    ->visible(fn (FarmRequest $record) => $record->status === 'draft'),
                DeleteAction::make()
                    ->visible(fn (FarmRequest $record) => $record->status === 'draft'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'submitted' => 'Submitted',
                        'approved'  => 'Approved',
                        'rejected'  => 'Rejected',
                        'fulfilled' => 'Fulfilled',
                    ]),
                Tables\Filters\SelectFilter::make('request_type')
                    ->label('Type')
                    ->options(array_combine(
                        FarmRequest::REQUEST_TYPES,
                        array_map('ucfirst', FarmRequest::REQUEST_TYPES)
                    )),
            ]);
    }
}
