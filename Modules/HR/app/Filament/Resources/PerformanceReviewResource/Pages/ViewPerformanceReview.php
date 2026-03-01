<?php

namespace Modules\HR\Filament\Resources\PerformanceReviewResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\PerformanceReviewResource;

class ViewPerformanceReview extends ViewRecord
{
    protected static string $resource = PerformanceReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Review Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('employee.full_name')
                            ->label('Employee')
                            ->getStateUsing(fn ($record) => $record->employee->first_name . ' ' . $record->employee->last_name)
                            ->weight('bold'),
                        TextEntry::make('reviewer.full_name')
                            ->label('Reviewer')
                            ->getStateUsing(fn ($record) => $record->reviewer
                                ? $record->reviewer->first_name . ' ' . $record->reviewer->last_name
                                : '—')
                            ->placeholder('—'),
                        TextEntry::make('performanceCycle.name')->label('Performance Cycle'),
                        TextEntry::make('company.name')->label('Company'),
                        TextEntry::make('rating')
                            ->label('Rating')
                            ->suffix(' / 5')
                            ->placeholder('Not rated'),
                    ]),

                Section::make('Comments')
                    ->schema([
                        TextEntry::make('comments')->columnSpanFull()->placeholder('No comments recorded.'),
                    ]),
            ]);
    }
}