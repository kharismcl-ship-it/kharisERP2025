<?php

namespace Modules\Construction\Filament\Resources\ProjectPhaseResource\Pages;

use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\ViewRecord;
use Modules\Construction\Filament\Resources\ProjectPhaseResource;

class ViewProjectPhase extends ViewRecord
{
    protected static string $resource = ProjectPhaseResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Phase Details')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('project.name')->label('Project'),
                    TextEntry::make('name'),
                    TextEntry::make('order'),
                ]),
                Grid::make(3)->schema([
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'completed'   => 'success',
                            'in_progress' => 'warning',
                            'pending'     => 'gray',
                            'on_hold'     => 'danger',
                            default       => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                    TextEntry::make('progress_percent')->label('Progress')->suffix('%'),
                ]),
                TextEntry::make('description')->columnSpanFull(),
            ]),

            Section::make('Timeline')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('planned_start')->label('Planned Start')->date(),
                    TextEntry::make('planned_end')->label('Planned End')->date(),
                ]),
                Grid::make(2)->schema([
                    TextEntry::make('actual_start')->label('Actual Start')->date(),
                    TextEntry::make('actual_end')->label('Actual End')->date(),
                ]),
            ]),

            Section::make('Budget')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('budget')->money('GHS'),
                    TextEntry::make('spent')->money('GHS'),
                ]),
            ]),
        ]);
    }
}
