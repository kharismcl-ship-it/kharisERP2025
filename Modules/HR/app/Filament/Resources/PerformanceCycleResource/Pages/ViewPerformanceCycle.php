<?php

namespace Modules\HR\Filament\Resources\PerformanceCycleResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\PerformanceCycleResource;

class ViewPerformanceCycle extends ViewRecord
{
    protected static string $resource = PerformanceCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Cycle Information')
                ->columns(2)
                ->schema([
                    TextEntry::make('company.name')->label('Company'),
                    TextEntry::make('name')->weight('bold'),
                    TextEntry::make('start_date')->date()->label('Start Date'),
                    TextEntry::make('end_date')->date()->label('End Date'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'open'    => 'success',
                            'planned' => 'info',
                            'closed'  => 'gray',
                            default   => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => ucfirst($state)),
                ]),

            Section::make('Summary')
                ->columns(3)
                ->schema([
                    TextEntry::make('total_reviews')
                        ->label('Total Reviews')
                        ->getStateUsing(fn ($record) => $record->performanceReviews()->count()),
                    TextEntry::make('submitted_reviews')
                        ->label('Submitted')
                        ->getStateUsing(fn ($record) => $record->performanceReviews()
                            ->whereIn('status', ['submitted', 'completed'])->count()),
                    TextEntry::make('pending_reviews')
                        ->label('Pending')
                        ->getStateUsing(fn ($record) => $record->performanceReviews()
                            ->where('status', 'pending')->count()),
                ]),

            Section::make('Description')
                ->collapsible()
                ->schema([
                    TextEntry::make('description')->placeholder('None'),
                ]),

            Section::make('Audit')
                ->columns(2)
                ->collapsible()->collapsed()
                ->schema([
                    TextEntry::make('created_at')->dateTime()->label('Created'),
                    TextEntry::make('updated_at')->dateTime()->label('Last Updated'),
                ]),
        ]);
    }
}