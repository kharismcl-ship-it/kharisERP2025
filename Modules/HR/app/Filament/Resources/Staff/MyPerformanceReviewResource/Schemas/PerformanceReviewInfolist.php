<?php

namespace Modules\HR\Filament\Resources\Staff\MyPerformanceReviewResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PerformanceReviewInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Review Summary')
                ->columns(3)
                ->schema([
                    TextEntry::make('performanceCycle.name')
                        ->label('Performance Cycle')
                        ->placeholder('—'),

                    TextEntry::make('reviewer.full_name')
                        ->label('Reviewed By')
                        ->placeholder('—'),

                    TextEntry::make('rating')
                        ->label('Overall Rating')
                        ->suffix(' / 5')
                        ->numeric(decimalPlaces: 1)
                        ->color(fn ($state) => match (true) {
                            $state >= 4   => 'success',
                            $state >= 2.5 => 'warning',
                            default       => 'danger',
                        })
                        ->placeholder('—'),

                    TextEntry::make('comments')
                        ->label('Reviewer Comments')
                        ->columnSpanFull()
                        ->placeholder('No comments provided.'),
                ]),

            Section::make('KPI Scores')
                ->visible(fn ($record) => $record->kpiScores()->exists())
                ->schema([
                    TextEntry::make('kpiScores')
                        ->label('')
                        ->columnSpanFull()
                        ->getStateUsing(function ($record): string {
                            return $record->kpiScores->map(function ($kpi) {
                                $kpiName = $kpi->kpiDefinition?->name ?? "KPI #{$kpi->id}";
                                return "• {$kpiName}: {$kpi->actual_value} / {$kpi->target_value} (Score: {$kpi->score})";
                            })->join("\n");
                        })
                        ->placeholder('No KPI scores recorded.'),
                ]),
        ]);
    }
}
