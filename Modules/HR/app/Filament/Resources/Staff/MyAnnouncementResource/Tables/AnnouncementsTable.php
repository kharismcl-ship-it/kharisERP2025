<?php

namespace Modules\HR\Filament\Resources\Staff\MyAnnouncementResource\Tables;

use Filament\Tables\Table;
use Filament\Tables;

class AnnouncementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'urgent'  => 'danger',
                        'high'    => 'warning',
                        'normal'  => 'info',
                        'low'     => 'gray',
                        default   => 'gray',
                    }),
                Tables\Columns\TextColumn::make('title')
                    ->weight('bold')
                    ->wrap(),
                Tables\Columns\TextColumn::make('target_audience')
                    ->label('Audience')
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('published_at')->date()->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->date()
                    ->label('Expires')
                    ->placeholder('No expiry'),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }
}
