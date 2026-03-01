<?php

namespace Modules\HR\Filament\Resources\JobVacancyResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\JobVacancyResource;
use Modules\HR\Models\JobVacancy;

class ViewJobVacancy extends ViewRecord
{
    protected static string $resource = JobVacancyResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Vacancy Overview')
                    ->collapsible()
                    ->columns(['default' => 2, 'xl' => 3])
                    ->schema([
                        TextEntry::make('title')->label('Position Title')->weight('bold')->columnSpanFull(),
                        TextEntry::make('company.name'),
                        TextEntry::make('department.name')->placeholder('—'),
                        TextEntry::make('jobPosition.title')->label('Job Position')->placeholder('—'),
                        TextEntry::make('employment_type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'full_time' => 'success', 'part_time' => 'warning',
                                'contract' => 'info', default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => JobVacancy::EMPLOYMENT_TYPES[$state] ?? $state),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'open' => 'success', 'draft' => 'gray',
                                'closed' => 'warning', 'filled' => 'info', default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                        TextEntry::make('vacancies_count')->label('Openings'),
                    ]),

                Section::make('Dates & Compensation')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('posted_date')->date()->placeholder('Not posted'),
                        TextEntry::make('closing_date')->date()->placeholder('Open-ended'),
                        TextEntry::make('salary_min')->money('GHS')->placeholder('Not specified'),
                        TextEntry::make('salary_max')->money('GHS')->placeholder('Not specified'),
                        TextEntry::make('postedBy.full_name')
                            ->label('Posted By')
                            ->getStateUsing(fn ($record) => $record->postedBy ? $record->postedBy->first_name . ' ' . $record->postedBy->last_name : '—'),
                    ]),

                Section::make('Job Description')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('description')->html()->columnSpanFull()->placeholder('No description'),
                    ]),

                Section::make('Requirements')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('requirements')->html()->columnSpanFull()->placeholder('No requirements listed'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Action::make('open')
                ->label('Open Vacancy')
                ->icon('heroicon-o-lock-open')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => in_array($this->record->status, ['draft', 'closed']))
                ->action(function () {
                    $this->record->update(['status' => 'open', 'posted_date' => now()]);
                    Notification::make()->title('Vacancy is now open')->success()->send();
                    $this->refreshFormData(['status', 'posted_date']);
                }),
            Action::make('close')
                ->label('Close')
                ->icon('heroicon-o-x-circle')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'open')
                ->action(function () {
                    $this->record->update(['status' => 'closed']);
                    Notification::make()->title('Vacancy closed')->warning()->send();
                    $this->refreshFormData(['status']);
                }),
            Action::make('markFilled')
                ->label('Mark Filled')
                ->icon('heroicon-o-check-badge')
                ->color('info')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status !== 'filled')
                ->action(function () {
                    $this->record->update(['status' => 'filled']);
                    Notification::make()->title('Vacancy marked as filled')->success()->send();
                    $this->refreshFormData(['status']);
                }),
        ];
    }
}