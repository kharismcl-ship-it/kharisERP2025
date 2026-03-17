<?php

namespace Modules\Hostels\Filament\Resources\Staff\MyVisitorLogResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Hostels\Models\VisitorLog;

class VisitorLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('visitor_name')
                    ->label('Visitor')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('hostel.name')
                    ->label('Hostel')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('occupant.full_name')
                    ->label('Visiting')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('visitor_phone')
                    ->label('Phone')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('purpose')
                    ->label('Purpose')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('check_in_at')
                    ->label('Checked In')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\IconColumn::make('check_out_at')
                    ->label('Out')
                    ->boolean()
                    ->getStateUsing(fn (VisitorLog $r) => $r->check_out_at !== null)
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),
            ])
            ->defaultSort('check_in_at', 'desc')
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->label('Update'),
                Action::make('check_out')
                    ->label('Check Out')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Record Check-Out?')
                    ->action(fn (VisitorLog $record) => $record->update(['check_out_at' => now()]))
                    ->visible(fn (VisitorLog $record) => $record->check_out_at === null),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('checked_out')
                    ->label('Checked Out')
                    ->queries(
                        true:  fn ($q) => $q->whereNotNull('check_out_at'),
                        false: fn ($q) => $q->whereNull('check_out_at'),
                    ),
            ]);
    }
}
