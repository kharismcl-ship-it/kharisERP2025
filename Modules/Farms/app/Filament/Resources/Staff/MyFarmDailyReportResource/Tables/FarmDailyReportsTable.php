<?php

namespace Modules\Farms\Filament\Resources\Staff\MyFarmDailyReportResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Farms\Models\FarmDailyReport;

class FarmDailyReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('report_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('farm.name')
                    ->label('Farm')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('summary')
                    ->limit(60)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'draft'     => 'gray',
                        'submitted' => 'warning',
                        'reviewed'  => 'success',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
            ])
            ->defaultSort('report_date', 'desc')
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (FarmDailyReport $record) => $record->status === 'draft'),
                Action::make('submit')
                    ->label('Submit')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Submit Daily Report?')
                    ->modalDescription('Once submitted, you will no longer be able to edit this report.')
                    ->action(fn (FarmDailyReport $record) => $record->update(['status' => 'submitted']))
                    ->visible(fn (FarmDailyReport $record) => $record->status === 'draft'),
                DeleteAction::make()
                    ->visible(fn (FarmDailyReport $record) => $record->status === 'draft'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'submitted' => 'Submitted',
                        'reviewed'  => 'Reviewed',
                    ]),
            ]);
    }
}
