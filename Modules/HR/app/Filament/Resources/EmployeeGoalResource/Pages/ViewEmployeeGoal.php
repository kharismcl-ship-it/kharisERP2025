<?php

namespace Modules\HR\Filament\Resources\EmployeeGoalResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\EmployeeGoalResource;
use Modules\HR\Models\EmployeeGoal;

class ViewEmployeeGoal extends ViewRecord
{
    protected static string $resource = EmployeeGoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('startProgress')
                ->label('Start Progress')
                ->icon('heroicon-o-play')
                ->color('info')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'not_started')
                ->action(function () {
                    $this->record->update(['status' => 'in_progress']);
                    $this->refreshFormData(['status']);
                    Notification::make()->title('Goal started')->send();
                }),
            Action::make('markComplete')
                ->label('Mark Complete')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'in_progress')
                ->action(function () {
                    $this->record->update(['status' => 'completed']);
                    $this->refreshFormData(['status']);
                    Notification::make()->title('Goal completed')->success()->send();
                }),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Goal Overview')->columns(2)->schema([
                TextEntry::make('employee_name')->label('Employee')
                    ->getStateUsing(fn ($record) => $record->employee->first_name . ' ' . $record->employee->last_name)
                    ->weight('bold'),
                TextEntry::make('title')->label('Goal Title'),
                TextEntry::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'not_started' => 'gray',
                        'in_progress' => 'info',
                        'completed'   => 'success',
                        'cancelled'   => 'danger',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => EmployeeGoal::STATUSES[$state] ?? ucwords(str_replace('_', ' ', $state))),
                TextEntry::make('priority')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'high'   => 'danger',
                        'medium' => 'warning',
                        'low'    => 'gray',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => EmployeeGoal::PRIORITIES[$state] ?? ucfirst($state)),
                TextEntry::make('due_date')->date()->placeholder('—'),
            ]),
            Section::make('Progress')->columns(3)->schema([
                TextEntry::make('target_value')->label('Target')->placeholder('—'),
                TextEntry::make('actual_value')->label('Achieved')->placeholder('—'),
                TextEntry::make('completion_pct')->label('Completion %')->suffix('%')
                    ->getStateUsing(fn (EmployeeGoal $record) => $record->completion_percentage),
            ]),
            Section::make('Description')->schema([
                TextEntry::make('description')->columnSpanFull()->placeholder('—'),
                TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }
}