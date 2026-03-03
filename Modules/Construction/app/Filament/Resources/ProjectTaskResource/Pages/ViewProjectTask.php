<?php

namespace Modules\Construction\Filament\Resources\ProjectTaskResource\Pages;

use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\ViewRecord;
use Modules\Construction\Filament\Resources\ProjectTaskResource;
use Modules\Construction\Models\ProjectTask;

class ViewProjectTask extends ViewRecord
{
    protected static string $resource = ProjectTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Task Details')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('project.name')->label('Project'),
                    TextEntry::make('phase.name')->label('Phase')->placeholder('—'),
                    TextEntry::make('contractor.name')->label('Contractor')->placeholder('Unassigned'),
                ]),
                TextEntry::make('name')->columnSpanFull(),
                Grid::make(3)->schema([
                    TextEntry::make('priority')
                        ->badge()
                        ->color(fn ($state): string => match ((int) $state) {
                            1 => 'gray',
                            2 => 'warning',
                            3 => 'danger',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => ProjectTask::PRIORITIES[(int) $state] ?? $state),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'completed'   => 'success',
                            'in_progress' => 'warning',
                            'blocked'     => 'danger',
                            'pending'     => 'gray',
                            default       => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                ]),
                TextEntry::make('description')->columnSpanFull(),
                TextEntry::make('notes')->columnSpanFull(),
            ]),

            Section::make('Dates')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('due_date')->label('Due Date')->date(),
                    TextEntry::make('completed_at')->label('Completed At')->date()->placeholder('—'),
                ]),
            ]),
        ]);
    }
}
