<?php

namespace Modules\HR\Filament\Resources\GrievanceCaseResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\GrievanceCaseResource;
use Modules\HR\Models\GrievanceCase;

class ViewGrievanceCase extends ViewRecord
{
    protected static string $resource = GrievanceCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('investigate')
                ->label('Start Investigation')
                ->icon('heroicon-o-magnifying-glass')
                ->color('info')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'filed')
                ->action(function () {
                    $this->record->update(['status' => 'under_investigation']);
                    $this->refreshFormData(['status']);
                    Notification::make()->title('Investigation started')->send();
                }),
            Action::make('scheduleHearing')
                ->label('Schedule Hearing')
                ->icon('heroicon-o-calendar')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'under_investigation')
                ->action(function () {
                    $this->record->update(['status' => 'hearing_scheduled']);
                    $this->refreshFormData(['status']);
                    Notification::make()->title('Hearing scheduled')->warning()->send();
                }),
            Action::make('resolve')
                ->label('Mark Resolved')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => in_array($this->record->status, ['filed', 'under_investigation', 'hearing_scheduled']))
                ->action(function () {
                    $this->record->update(['status' => 'resolved', 'resolution_date' => now()]);
                    $this->refreshFormData(['status', 'resolution_date']);
                    Notification::make()->title('Grievance resolved')->success()->send();
                }),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Case Overview')->columns(2)->schema([
                TextEntry::make('grievance_type')->label('Type')
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state ?? '')))
                    ->weight('bold'),
                TextEntry::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'filed'               => 'gray',
                        'under_investigation' => 'info',
                        'hearing_scheduled'   => 'warning',
                        'resolved'            => 'success',
                        'closed'              => 'gray',
                        'escalated'           => 'danger',
                        default               => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => GrievanceCase::STATUSES[$state] ?? ucfirst($state)),
                TextEntry::make('employee_name')->label('Employee')
                    ->getStateUsing(fn (GrievanceCase $record) => $record->is_anonymous
                        ? 'Anonymous'
                        : ($record->employee ? $record->employee->first_name . ' ' . $record->employee->last_name : '—')),
                TextEntry::make('company.name')->label('Company'),
                IconEntry::make('is_anonymous')->label('Anonymous')->boolean(),
                TextEntry::make('filed_date')->date()->label('Filed On')->placeholder('—'),
                TextEntry::make('resolution_date')->date()->label('Resolved On')->placeholder('Pending'),
                TextEntry::make('assigned_to_name')->label('Assigned To')
                    ->getStateUsing(fn (GrievanceCase $record) => $record->assignedTo
                        ? $record->assignedTo->first_name . ' ' . $record->assignedTo->last_name : '—')
                    ->placeholder('—'),
            ]),
            Section::make('Details')->schema([
                TextEntry::make('description')->label('Grievance Description')->columnSpanFull()->placeholder('—'),
                TextEntry::make('resolution')->label('Resolution Notes')->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }
}
