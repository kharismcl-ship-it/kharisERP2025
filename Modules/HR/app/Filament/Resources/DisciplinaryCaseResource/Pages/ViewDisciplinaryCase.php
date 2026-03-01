<?php

namespace Modules\HR\Filament\Resources\DisciplinaryCaseResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\DisciplinaryCaseResource;
use Modules\HR\Models\DisciplinaryCase;

class ViewDisciplinaryCase extends ViewRecord
{
    protected static string $resource = DisciplinaryCaseResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Case Overview')
                    ->collapsible()
                    ->columns(['default' => 2, 'xl' => 3])
                    ->schema([
                        TextEntry::make('employee.full_name')
                            ->label('Employee')
                            ->getStateUsing(fn ($r) => $r->employee->first_name . ' ' . $r->employee->last_name)
                            ->weight('bold'),
                        TextEntry::make('company.name'),
                        TextEntry::make('type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'verbal_warning' => 'gray', 'written_warning' => 'warning',
                                'final_warning' => 'danger', 'suspension' => 'danger',
                                'termination' => 'danger', default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => DisciplinaryCase::TYPES[$state] ?? $state),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'open' => 'danger', 'under_review' => 'warning',
                                'resolved' => 'success', 'appealed' => 'info', default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => DisciplinaryCase::STATUSES[$state] ?? $state),
                        TextEntry::make('incident_date')->date(),
                        TextEntry::make('handledBy.full_name')
                            ->label('Handled By')
                            ->getStateUsing(fn ($r) => $r->handledBy ? $r->handledBy->first_name . ' ' . $r->handledBy->last_name : '—'),
                    ]),

                Section::make('Incident Details')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('incident_description')->columnSpanFull(),
                        TextEntry::make('action_taken')->columnSpanFull()->placeholder('No action recorded'),
                    ]),

                Section::make('Resolution')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('resolution_date')->date()->placeholder('Not resolved'),
                        TextEntry::make('resolution_notes')->columnSpanFull()->placeholder('—'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Action::make('resolve')
                ->label('Resolve Case')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => in_array($this->record->status, ['open', 'under_review']))
                ->action(function () {
                    $this->record->update(['status' => 'resolved', 'resolution_date' => now()]);
                    Notification::make()->title('Case resolved')->success()->send();
                    $this->refreshFormData(['status', 'resolution_date']);
                }),
            Action::make('close')
                ->label('Close Case')
                ->icon('heroicon-o-lock-closed')
                ->color('gray')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'resolved')
                ->action(function () {
                    $this->record->update(['status' => 'closed']);
                    Notification::make()->title('Case closed')->send();
                    $this->refreshFormData(['status']);
                }),
        ];
    }
}
