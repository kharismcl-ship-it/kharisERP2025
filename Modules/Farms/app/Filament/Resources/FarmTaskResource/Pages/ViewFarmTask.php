<?php

namespace Modules\Farms\Filament\Resources\FarmTaskResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Farms\Filament\Resources\FarmTaskResource;

class ViewFarmTask extends ViewRecord
{
    protected static string $resource = FarmTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('complete')
                ->label('Mark Complete')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => ! $this->record->completed_at)
                ->action(fn () => $this->record->update(['completed_at' => now()])),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Task Overview')
                ->columns(3)
                ->schema([
                    TextEntry::make('priority')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'urgent' => 'danger',
                            'high'   => 'warning',
                            'medium' => 'info',
                            'low'    => 'gray',
                            default  => 'gray',
                        }),

                    TextEntry::make('task_type')
                        ->badge()
                        ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state)))
                        ->color('primary'),

                    TextEntry::make('farm.name')->label('Farm'),

                    TextEntry::make('assignedWorker.name')
                        ->label('Assigned To')
                        ->getStateUsing(fn ($record) => $record->assignedWorker?->display_name)
                        ->placeholder('Unassigned'),

                    TextEntry::make('due_date')
                        ->date('d M Y')
                        ->label('Due Date')
                        ->placeholder('—')
                        ->color(fn ($state, $record) =>
                            $state && now()->gt($state) && ! $record->completed_at ? 'danger' : null
                        ),

                    TextEntry::make('completed_at')
                        ->label('Completed At')
                        ->dateTime('d M Y H:i')
                        ->placeholder('Not yet completed')
                        ->color(fn ($state) => $state ? 'success' : 'warning'),
                ]),

            Section::make('Links')
                ->columns(3)
                ->collapsible()
                ->schema([
                    TextEntry::make('plot.name')->label('Plot')->placeholder('—'),
                    TextEntry::make('cropCycle.crop_name')->label('Crop Cycle')->placeholder('—'),
                    TextEntry::make('livestockBatch.batch_reference')->label('Livestock Batch')->placeholder('—'),
                ]),

            Section::make('Description')
                ->schema([
                    TextEntry::make('description')->columnSpanFull()->placeholder('—'),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([
                    TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
                ]),

            Section::make('Audit')
                ->collapsible()
                ->collapsed()
                ->columns(2)
                ->schema([
                    TextEntry::make('created_at')->dateTime('d M Y H:i'),
                    TextEntry::make('updated_at')->dateTime('d M Y H:i'),
                ]),
        ]);
    }
}