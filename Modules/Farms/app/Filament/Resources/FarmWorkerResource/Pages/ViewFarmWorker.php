<?php

namespace Modules\Farms\Filament\Resources\FarmWorkerResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Farms\Filament\Resources\FarmWorkerResource;

class ViewFarmWorker extends ViewRecord
{
    protected static string $resource = FarmWorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make(), DeleteAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Worker Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('display_name')
                        ->label('Name')
                        ->getStateUsing(fn ($record) => $record->display_name),

                    TextEntry::make('role')
                        ->badge()
                        ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state)))
                        ->color('primary'),

                    TextEntry::make('farm.name')->label('Farm'),

                    TextEntry::make('employee.jobPosition.name')
                        ->label('HR Job Title')
                        ->placeholder('Not linked to HR'),

                    TextEntry::make('daily_rate')
                        ->money('GHS')
                        ->label('Daily Rate')
                        ->placeholder('—'),

                    IconEntry::make('is_active')->label('Active')->boolean(),
                ]),

            Section::make('HR Employee Leave Status')
                ->columns(3)
                ->visible(fn ($record) => $record->employee_id !== null)
                ->schema([
                    TextEntry::make('hr_status')
                        ->label('Current Status')
                        ->getStateUsing(function ($record) {
                            $emp = $record->employee;
                            if (! $emp) {
                                return 'Not linked';
                            }
                            $onLeave = \Modules\HR\Models\LeaveRequest::where('employee_id', $emp->id)
                                ->where('status', 'approved')
                                ->whereDate('start_date', '<=', now())
                                ->whereDate('end_date', '>=', now())
                                ->exists();
                            return $onLeave ? 'On Leave' : 'Available';
                        })
                        ->color(fn ($state) => $state === 'On Leave' ? 'danger' : 'success'),

                    TextEntry::make('open_tasks_count')
                        ->label('Open Tasks')
                        ->getStateUsing(fn ($record) => $record->tasks()->whereNull('completed_at')->count()),

                    TextEntry::make('overdue_tasks_count')
                        ->label('Overdue Tasks')
                        ->getStateUsing(fn ($record) =>
                            $record->tasks()
                                ->whereNull('completed_at')
                                ->whereNotNull('due_date')
                                ->whereDate('due_date', '<', now())
                                ->count()
                        )
                        ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
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