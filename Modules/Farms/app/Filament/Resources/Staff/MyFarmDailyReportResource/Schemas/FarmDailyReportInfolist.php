<?php

namespace Modules\Farms\Filament\Resources\Staff\MyFarmDailyReportResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FarmDailyReportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Report Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('report_date')
                        ->date(),

                    TextEntry::make('status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'draft'     => 'gray',
                            'submitted' => 'warning',
                            'reviewed'  => 'success',
                            default     => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => ucfirst($state)),

                    TextEntry::make('farm.name')
                        ->label('Farm')
                        ->placeholder('—'),

                    TextEntry::make('weather_observation')
                        ->label('Weather')
                        ->placeholder('—'),

                    TextEntry::make('summary')
                        ->columnSpanFull()
                        ->placeholder('—'),

                    TextEntry::make('activities_done')
                        ->label('Activities Done')
                        ->columnSpanFull()
                        ->placeholder('—'),

                    TextEntry::make('issues_noted')
                        ->label('Issues Noted')
                        ->columnSpanFull()
                        ->placeholder('—'),

                    TextEntry::make('recommendations')
                        ->columnSpanFull()
                        ->placeholder('—'),
                ]),

            Section::make('Review')
                ->columns(2)
                ->visible(fn ($record) => $record->status === 'reviewed')
                ->schema([
                    TextEntry::make('reviewedBy.name')
                        ->label('Reviewed By')
                        ->placeholder('—'),

                    TextEntry::make('reviewed_at')
                        ->label('Reviewed At')
                        ->dateTime()
                        ->placeholder('—'),
                ]),
        ]);
    }
}
